<style scoped>
    
</style>

<template>
    
    <div>
        
        <textarea 
            :id="uniqId" 
            :name="name" 
            v-model="dataValue">
        </textarea>

    </div>

</template>

<script>
    
    export default {

        data(){
            return{
                dataValue: this.value ? this.value : '',
                tinyMCE_options: {},
                editor: null,
                uniqId: Math.random().toString(36).substring(7) + '_',
            };
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
            css: {
                type: String,
                default: '',
                required: false
            },
            assetsUrl: {
                type: String,
                default: '',
                required: false
            },
            editorOptions: {
                type: Object,
                required: false,
                default(){
                    return {};
                }
            }
        },
        
        watch: {
            value: function(val){
                this.dataValue = val ? val : '';
                if(this.editor){
                    this.editor.setContent(this.dataValue);
                }
            },
            /*assetsUrl: function(value){
                this.init();
            },
            editorOptions: function(value){
                this.init();
            },
            css: function(value){
                this.init();
            }*/
        },

        mounted() {
            this.init();
        },

        methods: {
            init(){
                var self = this;

                let elt = $("textarea#" + this.uniqId);

                console.log("textarea#" + this.uniqId, elt.length);

                this.tinyMCE_options = {
                    selector: "textarea#" + this.uniqId,
                    height: 300,
                    resize: "vertical",
                    language : 'fr_FR',
                    theme: "modern",
                    body_class: "tinymce-body",
                    content_css : this.css,
                    document_base_url: '/',
                    relative_urls: true,
                    convert_urls: false,
                    remove_script_host: true,
                    schema: "html5",
                    inline: false,
                    statusbar: false,
                    forced_root_block: false, // 'p'
                    //media_filter_html: true,
                    //extended_valid_elements:"iframe[src|title|width|height|allowfullscreen|frameborder|class|id],object[classid|width|height|codebase|*],param[name|value|_value|*],embed[type|width|height|src|*]",
                    //extended_valid_elements : "iframe[src|width|height|name|align|allowfullscreen|frameborder]",
                    //fontsize_formats: "8px 9px 10px 11px 12px 13px 14px 15px 16px 18px 20px 22px 24px 26px 28px 30px 36px 42px",
                    //image_list: tinyMceImages,
                    //link_list: tinyMceLinks,
                    plugins: [
                        "textcolor advlist autolink lists link image charmap print preview anchor emoticons", // media
                        "searchreplace visualblocks code fullscreen charmap",
                        "insertdatetime table contextmenu paste" //moxiemanager
                    ],
                    //menubar: "tools table format view insert edit",
                    menubar: false,
                    //toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
                    toolbar: "undo redo | bold italic | fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | blockquote | code",
                    textcolor_map: {},
                    //toolbar2: "fontsizeselect | forecolor backcolor | charmap | emoticons | media",
                    /*style_formats: [
                     {title: 'Bold text', inline: 'b'},
                     {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                     {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                     {title: 'Example 1', inline: 'span', classes: 'example1'},
                     {title: 'Example 2', inline: 'span', classes: 'example2'},
                     {title: 'Table styles'},
                     {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                     ],*/

                    setup: function(editor) {

                        editor.on('init', function(e) {
                            console.log('Editor initialized', self.uniqId);
                            self.editor = editor;
                            editor.setContent(self.dataValue ? self.dataValue : '');
                        });

                        editor.on('Change', function() {
                            self.dataValue = editor.getContent();
                        });

                        editor.on('blur', function() {
                            self.dataValue = editor.getContent();
                            self.updateValue(self.dataValue);
                        });
                    }
                };
                    
                this.tinyMCE_options = Object.assign({}, this.tinyMCE_options, this.editorOptions);

                if(this.assetsUrl){

                    axios.get(this.assetsUrl).then(response => {
                        
                        var medias = [];
                        
                        _.forEach(response.data, function(media){
                            medias.push({
                                title: media.title + ' (' + media.mime + ')',
                                value: media.filepath
                            });
                        });
                        
                        var opts = {
                            image_list: medias,
                            link_list: medias
                        };
                        this.tinyMCE_options = Object.assign({}, this.tinyMCE_options, opts);
                        
                        tinymce.init(this.tinyMCE_options).then(r => {
                            console.log('Editor initing done', this.uniqId, r);
                        });
                        console.log('Editor initing...', this.uniqId);

                    });

                }else{
                    tinymce.init(this.tinyMCE_options);
                }
            },

            updateValue(value) {
            
                this.$emit('input', value);
                return value;
            },
        }
    }
</script>

