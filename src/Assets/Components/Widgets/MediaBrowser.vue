<style scoped lang="scss">
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

    <div class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                
                <!--div class="modal-header">
                    <div class="modal-title">
                        <input type="text"
                            class="form-control"
                            v-model="search.text"
                            name="search"
                            placeholder="Search..."
                            :autofocus="true">
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div-->
                
                <div class="modal-body">

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <input type="text"
                                class="form-control"
                                placeholder="Rechercher..."
                                v-model="search.text">
                        </div>
                        <div class="col-sm-6 text-right">
                            <pagination :pagination="pagination" @change="loadPage" align="right"></pagination>
                        </div>
                    </div>

                    <div class="card-deck">
                        <div class="card" 
                            v-for="media in pagination.data"
                            @click="toggle(media)" 
                            :class="{'text-white bg-success': isSelected(media.id)}">

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
                        </div>
                    </div>

                    <!--div class="row narrow">
                        <div class="col col-sm-3" v-for="media in pagination.data">
                            <div class="media" @click="toggle(media)" :class="{'active': isSelected(media.id)}">
                                <img :src="'/' + media.filepath + '?' + media.decache">
                                <div class="title">{{ media.title }}</div>
                            </div>
                        </div>
                    </div>

                    <pagination :pagination="pagination" @change="loadPage"></pagination-->

                    <div class="alert alert-danger" v-if="readErrors">
                        {{ readErrors }}
                    </div>
                    
                </div>

                <!-- Modal Actions -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" tabindex="0">Cancel</button>

                    <button type="button" class="btn btn-primary" @click="submit">
                        Confirm selection
                    </button>
                </div>

            </div>
        </div>
    </div>

</template>

<script>

    import Pagination from './Pagination.vue'

    export default {

        components: {
            Pagination
        },

        data(){
            return{
                search: {
                    text: '',
                },
                pagination: {},
                page: 1,
                readStatus: null,
                readErrors: null,
                dialog: null,
                selected: this.value
            };
        },

        props: {
            uriPrefix: {
                type: String,
                required: false,
                default: ''
            },
            value: {
                type: Array,
                default(){
                    return [];
                }
            },
            multiple: {
                type: Boolean,
                required: false,
                default: false
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
            value: {
                handler(value){
                    this.selected = value;
                }
            }
        },

        mounted() {

            this.dialog = $(this.$el).on('shown.bs.modal', function (e) {
                $(this).find('input').first().focus();
            });

        },

        methods: {

            read: function()
            {
                this.readStatus = 'loading';

                var url = '/admin/media?limit=3&page=' + this.page;
                url += '&text=' + this.search.text;

                axios.get(url).then(response => {

                    this.readStatus = 'success';
                    this.pagination = response.data;

                }).catch(response => {

                    this.readStatus = 'error';
                    this.readErrors = response.response.data;

                });
            },

            loadPage(p){
                this.page = p;
                this.read();
            },

            open(){
                this.dialog.modal('show');
                this.page = 1;
                this.read();
            },

            close(){
                this.dialog.modal('hide');
            },

            submit(){
                this.$emit('input', this.selected);
            },

            toggle(media){

                var idx = _.findIndex(this.selected, function(m) { return m.id == media.id; });
                
                if(this.multiple){

                    if(idx === -1){
                        this.selected.push(media);
                    }else{
                        this.selected.splice(idx, 1);
                    }

                }else{

                    if(idx === -1){
                        this.selected = [media];
                    }else{
                        this.selected = [];
                    }

                }
            },

            isSelected(id){
                var idx = _.findIndex(this.selected, function(m) { return m.id == id; });
                return (idx !== -1);
            },
        }
    }
</script>
