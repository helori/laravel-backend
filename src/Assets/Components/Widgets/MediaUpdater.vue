<style scoped>
    input.file-input{
        position: absolute;
        left: -9999px;
    }
    input.file-input + label {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }
    .ellipsis{
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .bottom{
        padding: 5px;
    }
    .infos{
        margin: 5px 0 0 0;
        text-align: center;
        line-height: 20px;
    }
    .item .infos{
        min-height: 40px;
    }
    .mime{
        color: black;
        font-weight: bold;
    }
    .size{
        font-weight: bold;
    }
    .dim{
        font-weight: bold;
    }
    .path{
        color: #888;
        font-size: 12px;
        font-style: italic;
    }
    .preview{
        position: relative;
        height: 0;
        padding-bottom: 50%;
        overflow: hidden;
        background: repeating-linear-gradient(
            -45deg,
            #666666,
            #666666 10px,
            #444444 10px,
            #444444 20px
        );
    }
    .modal-dialog .preview{
        padding-bottom: 60%;
    }
    .preview .image{
        z-index: 1;
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-position: center center;
        background-repeat: no-repeat;
        background-size: contain;
    }
    .preview video, 
    .preview iframe{
        z-index: 1;
        position: absolute;
        width: 100%; height: 100%;
        top: 0; bottom: 0;
        left: 0; right: 0;
        background: black;
        border: 0;
        box-shadow: none;
    }
    .preview .text-wrapper{
        display: table;
        z-index: 2;
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: #666;
    }
    .preview .text-wrapper .text{
        display: table-cell;
        vertical-align: middle;
        text-align: center;
        color: white;
        font-size: 12px;
    }
    .preview .display{
        position: absolute;
        z-index: 2;
    }
    .preview .cropper{
        position: absolute;
        z-index: 3;
        cursor: move;
    }
    p:last-child{
        margin: 0;
    }
    .grab{
        z-index: 4;
        position: absolute;
        width: 20px; height: 20px;
        border: yellow 4px solid;
    }
    .grab.top-left{
        top: 0; left: 0;
        border-right: 0;
        border-bottom: 0;
        cursor: nwse-resize;
    }
    .grab.top-right{
        top: 0; right: 0;
        border-left: 0;
        border-bottom: 0;
        cursor: nesw-resize;
    }
    .grab.bottom-left{
        bottom: 0; left: 0;
        border-right: 0;
        border-top: 0;
        cursor: nesw-resize;
    }
    .grab.bottom-right{
        bottom: 0; right: 0;
        border-left: 0;
        border-top: 0;
        cursor: nwse-resize;
    }
    .grab.top{
        left: 50%; top: 0; margin-left: -20px/2;
        border-left: 0;
        border-right: 0;
        border-bottom: 0;
        cursor: ns-resize;
    }
    .grab.bottom{
        left: 50%; bottom: 0; margin-left: -20px/2;
        border-left: 0;
        border-right: 0;
        border-top: 0;
        cursor: ns-resize;
    }
    .grab.left{
        left: 0; top: 50%; margin-top: -20px/2;
        border-right: 0;
        border-top: 0;
        border-bottom: 0;
        cursor: ew-resize;
    }
    .grab.right{
        right: 0; top: 50%; margin-top: -20px/2;
        border-left: 0;
        border-top: 0;
        border-bottom: 0;
        cursor: ew-resize;
    }
    .mask{
        z-index: 3;
        position: absolute;
        background: rgba(0, 0, 0, 0.5);
    }
    .help-block{
        margin-bottom: 0;
        font-style: italic;
        font-size: 12px;
    }
</style>

<template>

    <div>
        
        <!-- Update Dialog -->
        <div class="modal fade media-update-dialog"tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    
                    <div class="modal-body">
                        
                        <div v-if="item">

                            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                            <!-- Preview -->
                            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                            <div v-if="item.type === 'image'">

                                <div id="crop-wrapper" class="preview">
                                    <div class="image" :style="'background-image:url(/' + item.filepath + '?' + item.decache + ')'"></div>
                                    <div class="display" :style="'left:' + size_display.x + 'px;top:' + size_display.y + 'px;width:' + size_display.w + 'px;height:' + size_display.h + 'px;'">
                                        <div class="cropper" 
                                            :style="'left:' + size_cropper.x + 'px;top:' + size_cropper.y + 'px;width:' + size_cropper.w + 'px;height:' + size_cropper.h + 'px;'">

                                            <div class="grab top-left" type="top-left"></div>
                                            <div class="grab top-right" type="top-right"></div>
                                            <div class="grab bottom-left" type="bottom-left"></div>
                                            <div class="grab bottom-right" type="bottom-right"></div>
                                            <div class="grab top" type="top"></div>
                                            <div class="grab bottom" type="bottom"></div>
                                            <div class="grab left" type="left"></div>
                                            <div class="grab right" type="right"></div>
                                        </div>
                                    </div>
                                    <div class="mask mask-left" :style="'left:0;top:0;height:100%;width:' + (size_display.x + size_cropper.x) + 'px'"></div>
                                    <div class="mask mask-right" :style="'right:0;top:0;height:100%;width:' + (size_viewport.w - size_display.x - size_cropper.x - size_cropper.w) + 'px'"></div>
                                    <div class="mask mask-top" :style="'top:0;left:' + (size_display.x + size_cropper.x) + 'px;height:' + (size_display.y + size_cropper.y) + 'px;width:' + size_cropper.w + 'px'"></div>
                                    <div class="mask mask-bottom" :style="'bottom:0;left:' + (size_display.x + size_cropper.x) + 'px;height:' + (size_viewport.h - size_display.y - size_cropper.y - size_cropper.h) + 'px;width:' + size_cropper.w + 'px'"></div>
                                </div>

                                <!--div class="infos">
                                    <div class="dim">{{ size_final.w }} x {{ size_final.h }} px</div>
                                </div-->

                            </div>

                            <div v-else-if="item.type === 'pdf'">
                                <div class="preview">
                                    <iframe :src="'/' + item.filepath + '?' + item.decache"></iframe>
                                </div>
                            </div>

                            <div v-else-if="item.type === 'video'">
                                <div class="preview">
                                    <video controls>
                                        <source :src="'/' + item.filepath + '?' + item.decache" :type="item.mime" />
                                    </video>
                                </div>
                            </div>

                            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->
                            <!-- Form -->
                            <!-- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -->

                            <div class="form-row my-2">
                                <div class="col-sm-3">Type : </div>
                                <div class="col-sm-9">
                                    <span class="font-weight-bold">{{ item.mime }}</span>
                                </div>
                            </div>

                            <div class="form-row mb-2">
                                <div class="col-sm-3">Poids : </div>
                                <div class="col-sm-9">
                                    <span class="font-weight-bold">{{ item.size / 1000 }} ko</span>
                                </div>
                            </div>

                            <div class="form-row mb-2">
                                <div class="col-sm-3">Fichier : </div>
                                <div class="col-sm-9 ellipsis">
                                    <span class="font-weight-bold">{{ item.filepath }}</span>
                                </div>
                            </div>

                            <div class="form-group form-row mb-2">
                                <label :for="'title-' + id" class="col-sm-3 col-form-label">Titre :</label>
                                <div class="col-sm-9">
                                    <input type="text" 
                                        class="form-control" 
                                        :id="'title-' + id" 
                                        placeholder="" 
                                        v-model="item.title"
                                        @change="setState('modified')">
                                </div>
                            </div>

                            <div v-if="item.type === 'image'">

                                <div class="form-group form-row mb-2">
                                    <label :for="'force-width-value-' + id" class="col-sm-3 col-form-label">
                                        Dimensions :
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="d-flex justify-content-stretch">
                                            <div class="flex-grow-1">
                                                <div class="input-group">
                                                    <input type="number" 
                                                        class="form-control" 
                                                        :id="'force-width-value-' + id" 
                                                        v-model="force_width"
                                                        @change="updateForceWidth">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">px</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mx-2 py-2">x</div>
                                            <div class="flex-grow-1">
                                                <div class="input-group">
                                                    <input type="number" 
                                                        class="form-control" 
                                                        :id="'force-height-value-' + id" 
                                                        v-model="force_height"
                                                        @change="updateForceHeight">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">px</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group form-row mb-2">
                                    <label class="col-sm-3 col-form-label">Rotation :</label>
                                    <div class="col-sm-9">
                                        <div class="btn-group w-100">
                                            <button type="button"
                                                class="flex-grow-1 btn"
                                                :class="rotate === 0 ? 'btn-primary' : 'btn-light'"
                                                @click="rotate = 0; setState('modified')">
                                                Aucune
                                            </button>
                                            <button type="button"
                                                class="flex-grow-1 btn"
                                                :class="rotate === 90 ? 'btn-primary' : 'btn-light'"
                                                @click="rotate = 90; setState('modified')">
                                                <i class="fal fa-redo" data-fa-transform="flip-h"></i>
                                            </button>
                                            <button type="button"
                                                class="flex-grow-1 btn"
                                                :class="rotate === -90 ? 'btn-primary' : 'btn-light'"
                                                @click="rotate = -90; setState('modified')">
                                                <i class="fal fa-redo"></i>
                                            </button>
                                            <button type="button"
                                                class="flex-grow-1 btn"
                                                :class="rotate === 'v' ? 'btn-primary' : 'btn-light'"
                                                @click="rotate = 'v'; setState('modified')">
                                                <i class="fal fa-arrows-v"></i>
                                            </button>
                                            <button type="button"
                                                class="flex-grow-1 btn"
                                                :class="rotate === 'h' ? 'btn-primary' : 'btn-light'"
                                                @click="rotate = 'h'; setState('modified')">
                                                <i class="fal fa-arrows-h"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group form-row mb-0">
                                    <label class="col-sm-3 col-form-label">Compression :</label>
                                    <div class="col-sm-9">
                                        <div class="btn-group w-100">
                                            <button type="button"
                                                class="flex-grow-1 btn"
                                                :class="compression === 0 ? 'btn-primary' : 'btn-light'"
                                                @click="compression = 0; setState('modified')">
                                                Aucune
                                            </button>
                                            <button type="button"
                                                class="flex-grow-1 btn"
                                                :class="compression === 100 ? 'btn-primary' : 'btn-light'"
                                                @click="compression = 100; setState('modified')">
                                                100%
                                            </button>
                                            <button type="button"
                                                class="flex-grow-1 btn"
                                                :class="compression === 95 ? 'btn-primary' : 'btn-light'"
                                                @click="compression = 95; setState('modified')">
                                                95%
                                            </button>
                                            <button type="button"
                                                class="flex-grow-1 btn"
                                                :class="compression === 90 ? 'btn-primary' : 'btn-light'"
                                                @click="compression = 90; setState('modified')">
                                                90%
                                            </button>
                                            <button type="button"
                                                class="flex-grow-1 btn"
                                                :class="compression === 85 ? 'btn-primary' : 'btn-light'"
                                                @click="compression = 85; setState('modified')">
                                                85%
                                            </button>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            <div v-if="update_error" class="alert alert-danger mt-2">
                                <div class="text-center">
                                    <p><strong>{{ update_error.title }}</strong></p>
                                    <p>{{ update_error.message }}</p>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        
                        <div class="form-row w-100">
                            <div class="col-4">
                                <button type="button" class="btn btn-default btn-block" data-dismiss="modal">
                                    <i class="fal fa-times-circle"></i> Fermer
                                </button>
                            </div>
                            <div class="col-8">
                                <button type="button" 
                                    class="btn btn-block" 
                                    :class="(update_state === 'error') ? 'btn-danger' : ((update_state === 'modified') ? 'btn-warning' : 'btn-primary')"
                                    @click="updateMedia()"
                                    :disabled="update_state == 'pending'">

                                    <span v-show="update_state === 'pending'">
                                        <i class="fal fa-spinner fa-spin"></i> Modifications en cours...
                                    </span>

                                    <span v-show="update_state === 'done'">
                                        <i class="fal fa-check-circle"></i> C'est tout bon !
                                    </span>

                                    <span v-show="update_state === 'error'">
                                        <i class="fal fa-exclamation-triangle"></i> Ouch !
                                    </span>

                                    <span v-show="update_state === 'modified'">
                                        <i class="fal fa-save"></i> Enregistrer
                                    </span>

                                    <span v-show="update_state === 'none'">
                                        <i class="fal fa-save"></i> Enregistrer
                                    </span>

                                </button>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

</template>

<script>
    export default {
        data(){
            return{
                item: null,
                id: Math.floor(Math.random()*(9999-1000+1)+1000),

                size_viewport: {w: 0, h: 0},
                size_original: {w: 0, h: 0},
                size_display: {x: 0, y: 0, w: 0, h: 0},
                size_cropper: {x: 0, y: 0, w: 0, h: 0},
                size_final: {x: 0, y: 0, w: 0, h: 0},

                drag_type: null,
                drag_from: {
                    x: 0, y: 0,
                    cropper: {x: 0, y: 0, w: 0, h: 0}
                },

                force_width: 0,
                force_height: 0,
                compression: 0,
                rotate: 0,

                update_state: 'none',
                update_error: null
            };
        },

        props: {
            uriPrefix: {
                type: String,
                required: false,
                default: ''
            }
        },

        mounted()
        {
            var self = this;
            $(this.$el).find('.media-update-dialog').on('shown.bs.modal', function () {
                self.prepareEditor();
            });
            this.initCropperMouse();
        },

        methods: {
            openUpdateDialog: function (item)
            {
                this.item = item;
                this.resetDialog();
                $(this.$el).find('.media-update-dialog').modal('show');
            },

            resetDialog(){
                this.force_width = 0;
                this.force_height = 0;
                this.compression = 0;
                this.rotate = 0;
                this.update_state = 'none';
                this.update_error = null;
            },

            closeUpdateDialog: function ()
            {
                $(this.$el).find('.media-update-dialog').modal('hide');
            },

            prepareEditor: function()
            {
                var self = this;
                self.initCropper();

                var image = new Image();
                image.onload = function()
                {
                    console.log('image loaded');

                    self.size_original.w = this.width;
                    self.size_original.h = this.height;
                    var prop_image = self.size_original.w / self.size_original.h;

                    var viewport = $(self.$el).find('#crop-wrapper .image');
                    self.size_viewport.w = viewport.width();
                    self.size_viewport.h = Math.floor(viewport.height());
                    var prop_viewport = self.size_viewport.w / self.size_viewport.h;
                    
                    if(prop_image > prop_viewport){
                        self.size_display.x = 0;
                        self.size_display.w = self.size_viewport.w;
                        self.size_display.h = Math.round(self.size_viewport.w / prop_image);
                        self.size_display.y = Math.round((self.size_viewport.h - self.size_display.h) / 2);
                    }else{
                        self.size_display.y = 0;
                        self.size_display.h = self.size_viewport.h;
                        self.size_display.w = Math.round(self.size_viewport.h * prop_image);
                        self.size_display.x = Math.round((self.size_viewport.w - self.size_display.w) / 2);
                    }
                    self.size_cropper.x = 0;
                    self.size_cropper.y = 0;
                    self.size_cropper.w = self.size_display.w;
                    self.size_cropper.h = self.size_display.h;

                    self.updateFinalSize();

                    console.log('viewport', self.size_viewport.w, self.size_viewport.h);
                    console.log('display', self.size_display.x, self.size_display.y, self.size_display.w, self.size_display.h);
                    console.log('cropper', self.size_cropper.x, self.size_cropper.y, self.size_cropper.w, self.size_cropper.h);
                    console.log('final', self.size_final.x, self.size_final.y, self.size_final.w, self.size_final.h);
                };
                image.src = '/' + this.item.filepath + '?' + this.item.decache;
            },

            setState: function(state){
                this.update_state = state;
            },

            updateMedia: function()
            {
                this.setState('pending');
                this.update_error = null;

                var data = {
                    title: this.item.title,
                    copyright: this.item.copyright,
                    rect: this.size_final,
                    force_width: this.force_width,
                    force_height: this.force_height,
                    compression: this.compression,
                    rotate: this.rotate,
                };

                axios.put(this.uriPrefix + '/admin/media/' + this.item.id, data).then(r => {

                    //console.log('update success', response);
                    this.setState('done');
                    this.item = r.data;
                    this.rotate = 0;

                    this.prepareEditor();
                    this.updateFinalSize();

                    this.$emit('updated', this.item);

                }).catch(r => {

                    this.setState('error');
                    this.update_error = r.response.data;
                    if(r.response.data.media){
                        this.item = r.response.data.media;
                        this.prepareEditor();
                    }
                    console.log('error', r);

                });
            },

            updateForceWidth: function()
            {
                this.force_height = Math.floor(this.size_final.h * this.force_width / this.size_final.w);
            },

            updateForceHeight: function()
            {
                this.force_width = Math.floor(this.size_final.w * this.force_height / this.size_final.h);
            },

            updateFinalSize: function()
            {
                var scale = this.size_original.w / this.size_display.w;

                this.size_final.x = Math.round(this.size_cropper.x * scale);
                this.size_final.y = Math.round(this.size_cropper.y * scale);
                this.size_final.w = Math.round(this.size_cropper.w * scale);
                this.size_final.h = Math.round(this.size_cropper.h * scale);

                this.force_width = this.size_final.w;
                this.force_height = this.size_final.h;
            },

            // ----------------------------------------------------------------
            //  Crop
            // ----------------------------------------------------------------
            initCropperMouse: function()
            {
                var self = this;

                $(document).mouseup(function()
                {
                    self.drag_type = null;
                });

                $(document).mousemove(function(e)
                {
                    e.stopPropagation();
                    
                    if(self.drag_type == 'top')                 {self.dragTop(e);}
                    else if(self.drag_type == 'bottom')         {self.dragBottom(e);}
                    else if(self.drag_type == 'left')           {self.dragLeft(e);}
                    else if(self.drag_type == 'right')          {self.dragRight(e);}
                    else if(self.drag_type == 'top-left')       {self.dragTop(e); self.dragLeft(e);}
                    else if(self.drag_type == 'top-right')      {self.dragTop(e); self.dragRight(e);}
                    else if(self.drag_type == 'bottom-left')    {self.dragBottom(e); self.dragLeft(e);}
                    else if(self.drag_type == 'bottom-right')   {self.dragBottom(e); self.dragRight(e);}
                    else if(self.drag_type == 'move')           {self.dragMove(e);}
                    
                    if(self.drag_type !== null)
                    {
                        self.updateFinalSize();
                        self.updateForceSize();
                        self.setState('modified');
                    }
                });
            },

            // ----------------------------------------------------------------
            //  Crop with mouse
            // ----------------------------------------------------------------
            initCropper: function()
            {
                var self = this;
                var cropper = $(this.$el).find('.cropper');

                cropper.mousedown(function(e){
                    self.drag_type = 'move';
                    self.initDrag(e);
                });

                cropper.find('.grab').mousedown(function(e){
                    e.stopPropagation();
                    self.drag_type = $(this).attr('type');
                    self.initDrag(e);
                });
            },

            initDrag: function(e)
            {
                this.drag_from.x = e.pageX;
                this.drag_from.y = e.pageY;

                this.drag_from.cropper.x = this.size_cropper.x;
                this.drag_from.cropper.y = this.size_cropper.y;
                this.drag_from.cropper.w = this.size_cropper.w;
                this.drag_from.cropper.h = this.size_cropper.h;
            },

            dragMove: function(e)
            {
                var dx = e.pageX - this.drag_from.x;
                var dy = e.pageY - this.drag_from.y;

                dx = Math.max(-this.drag_from.cropper.x, dx);
                dx = Math.min(this.size_display.w - this.drag_from.cropper.x - this.drag_from.cropper.w, dx);

                dy = Math.max(-this.drag_from.cropper.y, dy);
                dy = Math.min(this.size_display.h - this.drag_from.cropper.y - this.drag_from.cropper.h, dy);

                this.size_cropper.x = this.drag_from.cropper.x + dx;
                this.size_cropper.y = this.drag_from.cropper.y + dy;
            },

            dragTop: function(e)
            {
                var display = $(this.$el).find('#crop-wrapper .display');
                var display_y = display.offset().top;

                var dy = Math.round(e.pageY - display_y);
                
                dy = Math.max(dy, 0);
                dy = Math.min(dy, this.size_cropper.y + this.size_cropper.h);
                
                this.size_cropper.h = this.size_cropper.y + this.size_cropper.h - dy;
                this.size_cropper.y = dy;
            },

            dragBottom: function(e)
            {
                var display = $(this.$el).find('#crop-wrapper .display');
                var display_y = display.offset().top;

                var h = Math.round(e.pageY - display_y - this.size_cropper.y);
                
                h = Math.max(h, 0);
                h = Math.min(h, this.size_display.h - this.size_cropper.y);
                
                this.size_cropper.h = h;
            },

            dragLeft: function(e)
            {
                var display = $(this.$el).find('#crop-wrapper .display');
                var display_x = display.offset().left;

                var dx = Math.round(e.pageX - display_x);
                
                dx = Math.max(dx, 0);
                dx = Math.min(dx, this.size_cropper.x + this.size_cropper.w);
                
                this.size_cropper.w = this.size_cropper.x + this.size_cropper.w - dx;
                this.size_cropper.x = dx;
            },

            dragRight: function(e)
            {
                var display = $(this.$el).find('#crop-wrapper .display');
                var display_x = display.offset().left;

                var w = Math.round(e.pageX - display_x - this.size_cropper.x);
                
                w = Math.max(w, 0);
                w = Math.min(w, this.size_display.w - this.size_cropper.x);
                
                this.size_cropper.w = w;
            }
        }
    }
</script>
