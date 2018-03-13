<style scoped lang="scss">
.media-input{
    overflow: hidden;
    .image{
        height: 90px;
    }
    img{
        max-width: 100%;
        max-height: 100%;
    }
    .title{
        font-weight: bold;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
}
</style>

<template>

    <div>

        <div class="media-input rounded border bg-white" v-if="dataValue">

            <div class="row no-gutters">
                <div class="col-sm-4" v-if="dataValue[0]">
                    <div class="image d-flex justify-content-center align-items-center bg-secondary">
                        <img :src="'/' + dataValue[0].filepath + '?' + dataValue[0].decache">
                    </div>
                </div>
                <div class="col-sm-4" v-if="dataValue[0]">
                    <div class="p-2">
                        <div class="title">{{ dataValue[0].title }}</div>
                        <div>{{ dataValue[0].mime }}</div>
                        <div>{{ dataValue[0].size / 1000 }} ko</div>
                        <div>{{ dataValue[0].width }} x {{ dataValue[0].height }} px</div>
                    </div>
                </div>
                <div class="col-sm-8" v-if="!dataValue[0]">
                    <div class="py-2 px-3">
                        <div class="form-control-plaintext font-italic text-secondary">Aucun fichier chargé</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-2 text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary" @click="browse()">    
                                <i class="fal fa-folder-open"></i>
                            </button>
                            <button type="button" class="btn btn-secondary" @click="openUpdate()" v-if="dataValue[0]">
                                <i class="fal fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger" @click="remove()" v-if="dataValue[0]">
                                <i class="fal fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <media-browser 
            ref="mediaBrowser"
            v-model="dataValue"
            @input="update">
        </media-browser>

        <media-updater 
            ref="mediaUpdater"
            @updated="setValue">
        </media-updater>

    </div>

</template>

<script>
    
    import MediaBrowser from './MediaBrowser.vue';
    import MediaUpdater from './MediaUpdater.vue';

    export default {

        components: {
            MediaBrowser,
            MediaUpdater
        },

        data(){
            return{
                dataValue: this.value
            }
        },

        props: {
            // "value" is required to use v-model on the component
            value: { 
                default: null
            },
            name: {
                type: String,
                default: ''
            },
            error: {
                default: null
            },
        },

        watch: {
            value: {
                handler: function (val) {
                    this.dataValue = val;
                }
            }
        },

        methods: {

            browse(){
                this.$refs.mediaBrowser.open();
            },

            update(selected){
                this.dataValue = selected;
                this.$emit('input', this.dataValue);
                this.$refs.mediaBrowser.close();
            },

            remove(media){
                if(media){
                    var idx = _.findIndex(this.dataValue, function(m) { return m.id == media.id; });
                    if(idx !== -1){
                        this.dataValue.splice(idx, 1);
                    }
                }else{
                    this.dataValue = [];
                }
                this.$emit('input', this.dataValue);
            },

            openUpdate(){
                this.$refs.mediaUpdater.openUpdateDialog(this.dataValue[0]);
            },

            setValue(value){
                console.log(value);
                this.dataValue = [value];
            },
        }
    }
</script>
