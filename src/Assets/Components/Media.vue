<style scoped lang="scss">
input.file-input{
    position: absolute;
    left: -9999px;
}
input.file-input + label {
    cursor: pointer;
}
input.file-input + label:hover,
input.file-input + label.disabled {
    background: white;
}
.card-img-top {
    height: 120px;
    object-fit: contain;
    img{
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
}
</style>

<template>
    <div>
        
        <h1 class="mb-3 text-center">Médias</h1>

        <input type="file" multiple
            accept="image/jpeg,image/png,application/pdf,video/mp4,video/m4v"
            :id="'file-input-' + id" 
            :disabled="uploadStatus == 'uploading'"
            class="file-input">

        <label :for="'file-input-' + id" 
            class="filedrop d-block" 
            :class="{'disabled': uploadStatus == 'uploading'}">

            <div class="card mb-3 bg-light text-center">
                <div class="card-body">

                    <h5 class="card-title">Zone de dépôt des fichiers</h5>
                    <p class="card-text">Cliquez ici pour sélectionner des fichiers<br>ou glissez-déposez directement vos fichiers dans cette zone !</p>

                    <div class="progress w-75 mx-auto mt-3" v-if="uploadStatus == 'uploading'">
                        <div class="progress-bar" 
                            role="progressbar" 
                            :style="'width:' + (100 * uploadProgress / uploadTotal) + '%'"
                            :aria-valuenow="(100 * uploadProgress / uploadTotal)" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                            {{ 100 * uploadProgress / uploadTotal | number(0) }} %
                        </div>
                    </div>

                    <!--button type="button" class="btn btn-warning btn-block" @click="cancel" v-if="uploadStatus == 'uploading'">
                        <i class="fa fa-close"></i> Cancel
                    </button-->

                </div>
            </div>

        </label>

        <div v-if="uploadError" class="alert alert-danger text-center">
            Erreur {{ uploadError.response.status }} : {{ uploadError.response.statusText }}
        </div>

        <div v-if="uploadStatus == 'done'" class="alert alert-success alert-dismissible text-center">
            Fichiers chargés avec succès !
        </div>

        <div class="row mb-3">
            <div class="col-sm-6">
                <input type="text"
                    autofocus
                    class="form-control"
                    placeholder="Rechercher..."
                    v-model="search.text">
            </div>
            <div class="col-sm-6 text-right">
                <pagination :pagination="pagination" @change="loadPage" align="right"></pagination>
            </div>
        </div>

        <div class="card-deck">
            <div class="card" v-for="media in pagination.data">

                <div class="card-img-top bg-dark"
                    v-if="media.mime.indexOf('image') !== -1">
                    <img class="d-block m-auto"
                        :src="'/' + media.filepath + '?' + media.decache" 
                        :alt="media.title" >
                </div>

                <div class="card-img-top bg-dark text-white"
                    v-if="media.mime.indexOf('image') === -1">
                </div>

                <div class="card-img-overlay text-center text-white"
                    v-if="media.mime.indexOf('image') === -1">
                    <h5 class="card-title">{{ media.mime }}</h5>
                </div>

                <div class="card-body text-center">
                    <h5 class="card-title">{{ media.title }}</h5>
                    <p class="card-text">
                        {{ media.mime }}<br>
                        {{ media.size / 1000 }} ko<br>
                        {{ media.width }} x {{ media.height }} px<br>
                    </p>
                </div>
                <div class="card-footer">
                    <div class="form-row">
                        <div class="col">
                            <!--button type="button" class="btn btn-primary btn-sm btn-block" @click="download(media)">
                                <i class="fal fa-download"></i>
                            </button-->
                            <a :href="'/admin/media/' + media.id + '/open'" 
                                class="btn btn-primary btn-sm btn-block" 
                                target="_blank">
                                <i class="fal fa-download"></i>
                            </a>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-primary btn-sm btn-block" @click="openUpdate(media)">
                                <i class="fal fa-edit"></i>
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-danger btn-sm btn-block" @click="destroy(media)">
                                <i class="fal fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <media-updater 
            ref="mediaUpdater"
            @updated="read()">
        </media-updater>

    </div>
</template>

<script>
    
    import MediaUpdater from './Widgets/MediaUpdater.vue';
    import Pagination from './Widgets/Pagination.vue';

    export default {

        components:{
            MediaUpdater,
            Pagination
        },
        
        data(){
            return {
                id: Math.floor(Math.random()*(9999-1000+1)+1000),
                files: null,
                search: {
                    text: '',
                },
                
                pagination: {},
                page: 1,

                selectAll: false,
                selected: [],

                loaded: false,
                //readStatus: 'none',

                uploadSource: null,
                uploadStatus: 'none',
                uploadProgress: 0,
                uploadTotal: 0,
                uploadError: null,

                destroyStatus: 'none',
                destroyError: null
            }
        },

        watch: {
            search: {
                handler(){
                    this.page = 1;
                    this.read();
                },
                deep: true
            },
        },

        mounted(){
            
            var self = this;

            // -------------------------------------------------------
            //  Init browse button
            // -------------------------------------------------------
            $(this.$el).on('change', '.file-input', function(e){
                self.files = e.target.files;
                self.upload();
            }).on('click', '.file-input', function(e){
                this.value = null;
            });

            // -------------------------------------------------------
            //  Init cancel token
            // -------------------------------------------------------
            var CancelToken = axios.CancelToken;
            this.uploadSource = CancelToken.source();

            // -------------------------------------------------------
            //  read items
            // -------------------------------------------------------
            this.read();

            // -------------------------------------------------------
            //  Prevent from openning file in browser on drop
            // -------------------------------------------------------
            window.addEventListener("dragover", function(e){
                e.preventDefault();
            }, false);

            window.addEventListener("drop", function(e){
                e.preventDefault();
            }, false);

            // -------------------------------------------------------
            //  Init drag and drop
            // -------------------------------------------------------
            $(this.$el).find('.filedrop').on('drop', function(e){
                e.preventDefault();
                e.stopPropagation();
                //console.log('files dropped', e.originalEvent.dataTransfer.files);
                self.files = e.originalEvent.dataTransfer.files;
                self.upload();
            });
        },

        methods: {

            read: function()
            {
                var url = '/admin/media?limit=4&page=' + this.page;
                url += '&text=' + this.search.text;
                this.loaded = false;

                axios.get(url).then(r => {

                    this.pagination = r.data;

                }).catch(r => {

                    

                });
            },

            loadPage(p){
                this.page = p;
                this.read();
            },

            upload: function()
            {
                var self = this;

                this.uploadStatus = 'uploading';
                this.uploadProgress = 0;
                this.uploadTotal = 0;
                this.uploadError = null;

                var formData = new FormData();
                for(var i=0; i<this.files.length; ++i){
                    formData.append('file-' + i, this.files[i], this.files[i].name);
                }

                var config = {
                    onUploadProgress: function(e) {
                        console.log('upload progress', e);
                        if (e.lengthComputable) {
                            self.uploadProgress = e.loaded;
                            self.uploadTotal = e.total;
                        }
                    },
                    //cancelToken: this.uploadSource
                };

                axios.post('/admin/media', formData, config).then(response => {
                    
                    this.uploadStatus = 'done';
                    this.read();
                    
                }).catch(response => {
                    
                    this.uploadStatus = 'none';
                    this.uploadError = response.data;
                });
            },

            cancel: function()
            {
                if(this.uploadSource){
                    console.log('upload cancel');
                    this.uploadSource.cancel();
                }else{
                    console.log('Cannot cancel', this.uploadSource);
                }
            },

            destroy(media) {

                this.destroyStatus = 'loading';
                
                axios.delete('/admin/media/' + media.id).then(response => {

                    this.read();
                    
                }).catch(response => {

                    

                });

            },

            download(media){
                window.location.href = '/admin/media/' + media.id + '/download';
            },

            openUpdate(item){
                this.$refs.mediaUpdater.openUpdateDialog(item);
            },
        }
    }
</script>
