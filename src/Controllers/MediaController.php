<?php

namespace Helori\LaravelBackend\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Routing\Controller;
use Helori\LaravelBackend\Models\Media;
use Image;


// sudo apt-get install gifsicle jpegoptim pngquant optipng

class MediaController extends Controller
{
    public function read(Request $request, $id = null)
    {
        if(!is_null($id)){

            return Media::findOrFail($id);

        }else{

            $limit = intVal($request->input('limit', 5));
            $search = $request->input('text', '');
            $order = 'created_at';
            $reverse = true;
            
            $query = Media::orderBy($order, $reverse ? 'desc' : 'asc');

            if($search){
                $query->where(function($q) use($search) {
                    $q->where('filename', 'like', '%'.$search.'%')
                        ->orWhere('title', 'like', '%'.$search.'%')
                        ->orWhere('mime', 'like', '%'.$search.'%')
                        ->orWhere('type', 'like', '%'.$search.'%')
                        ->orWhere('extension', 'like', '%'.$search.'%');
                });
            }

            if($limit == 0){
                return $query->get();
            }else{
                return $query->paginate($limit);
            }
        }
    }

    public function delete(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        $path = public_path().'/'.$media->filepath;
        if(is_file($path))
            unlink($path);
        
        $media->delete();
    }

    public function create(Request $request)
    {
        $file_path = 'storage/medias';
        $file_prefix = 'file-';
        $i = 0;

        if(!is_dir($file_path)){
            mkdir($file_path, 0777, true);
        }

        $file = $request->file($file_prefix.$i);
        if(!$file->isValid()){
            throw new \Exception($file->getErrorMessage());
        }

        $medias = [];

        while($request->hasFile($file_prefix.$i))
        {
            $file = $request->file($file_prefix.$i);

            if($file->isValid())
            {
                // -----------------------------------------------------------
                //  Create the media
                // -----------------------------------------------------------
                $media = new Media();
                $media->save();

                // -----------------------------------------------------------
                //  Get uploaded file infos && Move file
                // -----------------------------------------------------------
                $file_ext = $file->guessExtension();
                $file_name = $file->getClientOriginalName();
                $file_name = substr($file_name, 0, strripos($file_name, '.'));
                $file_name = Str::slug($media->id.'_'.$file_name, '_');

                // -----------------------------------------------------------
                //  Save the media
                // -----------------------------------------------------------
                $media->mime = $file->getMimeType();
                $media->size = $file->getClientSize();
                $media->extension = $file_ext;
                $media->filename = $file_name.'.'.$file_ext;
                $media->title = $file->getClientOriginalName();
                $media->filepath = $file_path.'/'.$media->filename;
                //$media->width = $img->width();
                //$media->height = $img->height();
                //$media->size = filesize($abs_path);
                
                $abs_path = public_path().'/'.$media->filepath;
                
                //$file->store($file_path);
                $file->move(public_path().'/'.$file_path, $media->filename);

                $media->size = filesize($abs_path);
                $media->decache = filemtime($abs_path);

                $is_image = (strpos($media->mime, 'image') !== false);
                $is_pdf = (strpos($media->mime, 'pdf') !== false);
                $is_video = (strpos($media->mime, 'video') !== false);

                if($is_image)
                {
                    $info = getimagesize($abs_path);
                    $media->type = 'image';
                    $media->width = isset($info[0]) ? $info[0] : 0;
                    $media->height = isset($info[1]) ? $info[1] : 0;
                    //$media->mime = isset($info['mime']) ? $info['mime'] : '';
                }
                else if($is_pdf)
                {
                    $media->type = 'pdf';
                }
                else if($is_video)
                {
                    $media->type = 'video';
                }

                $media->save();

                $max_width = config('laravel-backend.medias.images.max_width');
                $max_height = config('laravel-backend.medias.images.max_width');
                $compression_quality = config('laravel-backend.medias.images.compression_quality');

                $this->scaleImage($media, $max_width, $max_height);
                $this->optimizeImage($media, $compression_quality);

                $medias[] = $media;
            }
            else{
                throw new \Exception($file->getErrorMessage());
            }
            ++$i;
        }
        return $medias;
    }

    public function update(Request $request, $id)
    {
        $media = Media::findOrFail($id);

        // Rename
        $title = $request->input('title', null);
        if($title && $title != $media->title)
        {
            $file_dir = dirname($media->filepath);
            $file_name = Str::slug($media->id.'_'.$title, '_').'.'.$media->extension;
            $new_path = $file_dir.'/'.$file_name;

            if(file_exists(public_path().'/'.$media->filepath)){
                rename(public_path().'/'.$media->filepath, public_path().'/'.$new_path);
            }
            
            $media->title = $title;
            $media->filename = $file_name;
            $media->filepath = $new_path;
            $media->save();
        }

        // Copyright
        $copyright = $request->input('copyright', null);
        $media->copyright = $copyright;
        $media->save();

        if($media->type === 'image')
        {
            $rect = $request->input('rect', null);
            
            $force_width = intVal($request->input('force_width', 0));
            $force_height = intVal($request->input('force_height', 0));
            $compression = intVal($request->input('compression', 0));
            $rotate = $request->input('rotate', null);

            if($rect['w'] != $media->width || $rect['h'] != $media->height)
            {
                $response = $this->cropImage($media, $rect['x'], $rect['y'], $rect['w'], $rect['h']);
                if($response !== true){
                    return $response;
                }
            }
            if($force_width !== 0 && $force_height !== 0)
            {
                $response = $this->scaleImage($media, $force_width, $force_height);
                if($response !== true){
                    return $response;
                }
            }
            if($compression !== 0)
            {
                $response = $this->optimizeImage($media, $compression);
                if($response !== true){
                    return $response;
                }
            }
            if($rotate !== null)
            {
                $response = $this->rotateImage($media, $rotate);
                if($response !== true){
                    return $response;
                }
            }
        }

        return $media;
    }

    protected function cropImage(&$media, $x, $y, $w, $h)
    {
        ini_set('memory_limit', '4096M');
        clearstatcache();

        $abs_path = public_path().'/'.$media->filepath;

        $img = Image::make($abs_path);
        $img->crop($w, $h, $x, $y);
        $img->save($abs_path);

        $media->width = $w;
        $media->height = $h;
        $media->size = $img->filesize();
        $media->decache = filemtime($abs_path);

        $media->save();

        return true;
    }

    protected function rotateImage(&$media, $value = null)
    {
        if(is_null($value)){
            return false;
        }

        ini_set('memory_limit', '4096M');
        clearstatcache();

        $abs_path = public_path().'/'.$media->filepath;

        Image::configure(); // ['driver' => 'imagick']
        $img = Image::make($abs_path);

        if($value === 'v' || $value === 'h'){
            
            $img->flip($value);
        
        }else if($value !== 0){
            
            $img->rotate($value);
        }
        
        $img->save($abs_path);

        $media->width = $img->width();
        $media->height = $img->height();
        $media->size = $img->filesize();
        $media->decache = filemtime($abs_path);

        $media->save();

        return true;
    }

    protected function scaleImage(&$media, $width = null, $height = null)
    {
        ini_set('memory_limit', '4096M');
        clearstatcache();

        $abs_path = public_path().'/'.$media->filepath;

        Image::configure(); // ['driver' => 'imagick']
        $img = Image::make($abs_path);

        if($width > 0 && $width < $media->width){
            $img->widen($width);
            $media->width = $img->width();
            $media->height = $img->height();
        }
        else if($height > 0 && $height < $media->height){
            $img->heighten($height);
            $media->width = $img->width();
            $media->height = $img->height();
        }
        
        $img->save($abs_path);
        
        $media->size = $img->filesize();
        $media->decache = filemtime($abs_path);

        $media->save();

        return true;
    }

    protected function optimizeImage(&$media, $quality = 100)
    {
        if($quality === 0){
            return response()->json([
                'media' => $media,
                'title' => "Oups, la compression a échoué !",
                'message' => "La compression d'image ne peut pas être à 0"
            ], 500);
        }

        clearstatcache();
        
        $mimes = [
            'image/gif' => 'gif',
            'image/jpeg' => 'jpg',
            'image/pjpeg' => 'jpg',
            'image/png' => 'png',
            'image/x-png' => 'png',
        ];

        $cmds_default = [
            'gif' => 'gifsicle',
            'jpg' => 'jpegoptim',
            'png' => 'pngquant',
        ];

        $opts_default = [
            'gifsicle' => '-b -O2',
            'jpegoptim' => '-m'.$quality.' --strip-all',
            'pngquant' => '--ext=.png --force',
            'optipng' => '-o5 -strip all',
        ];

        //$cmds = config('laravel-crudui.images_cmds', $cmds_default);
        //$opts = config('laravel-crudui.images_cmds_opts', $opts_default);

        $cmds = $cmds_default;
        $opts = $opts_default;

        if(in_array($media->mime, array_keys($mimes)))
        {
            if(function_exists('exec'))
            {
                // on VPS, usually placed in /usr/bin/
                // on local machine, usually placed in /usr/local/bin/

                $type = $mimes[$media->mime];
                $cmd = $cmds[$type];
                $opt = $opts[$cmd];
                $abs_path = public_path().'/'.$media->filepath;

                $cmd_test = exec($cmd.' --version', $output, $result_code);
                
                if($result_code === 0)
                {
                    exec($cmd.' '.$opt.' '.escapeshellarg($abs_path), $output, $result_code);
                    if($result_code !== 0){
                        return response()->json([
                            'media' => $media,
                            'title' => "Oups, la compression a échoué !",
                            'message' => "Pour vous aider, nous avons besoin du code d'erreur ".$result_code.", de votre fichier... et de votre compréhension. Merci :)"
                        ], 500);
                    }
                    $media->size = filesize($abs_path);
                    $media->decache = filemtime($abs_path);
                    $media->save();
                }
                else{
                    return response()->json([
                        'media' => $media,
                        'title' => "Oups, la compression a échoué !",
                        'message' => "Pour compresser les images de type ".$media->mime.", notre petit robot a besoin que l'application ".$cmd." soit installée sur votre serveur."
                    ], 500);
                }
            }
            else{
                return response()->json([
                    'media' => $media,
                    'title' => "Oups, la compression a échoué !",
                    'message' => "Notre petit robot n'a pas le droit de lancer les programmes de votre serveur. Peut-être utilisez-vous un serveur mutualisé ?"
                ], 500);
            }
        }
        else{
            return response()->json([
                'media' => $media,
                'title' => "Oups, la compression a échoué !",
                'message' => "Les images de type ".$media->mime." ne peuvent pas être compressées ! Vous ne pouvez compresser que des JPEG, PNG et GIF."
            ], 500);
        }
        return true;
    }

    public function download(Request $request, $id)
    {
        $media = Media::findOrFail($id);
        $path = public_path().'/'.$media->filepath;
        return response()->download($path, $media->filename);
    }

    public function open(Request $request, $id)
    {
        $media = Media::findOrFail($id);
        $path = public_path().'/'.$media->filepath;

        $infos = pathinfo($path);
        $ext = $infos['extension'];

        return response()->make(file_get_contents($path), 200, [
            'Content-Type' => $media->mime,
            'Content-Disposition' => 'inline; '.$media->title.'.'.$ext,
        ]);
    }
}
