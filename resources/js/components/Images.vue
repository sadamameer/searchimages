<template>
    <div>
        <div v-if="loader" id="loader_transaparent"></div>

        <navbar
            :loader       = "loader"
            :FirstLoad    = "FirstLoad"
            :text         = "text"
        />

        <main-header
            :FirstLoad    = "FirstLoad"
            :text         = "text"
        />

        <images-grid
            v-if="ViewType == 'grid'"
            :PerPage      = "per_page"
        />

        <images-cube
            v-if="ViewType == 'cube'"
            :PerPage      = "per_page"
        />
    </div>
</template>

<script>
    export default {
        data(){
            return {
                loader          : false,
                FirstLoad       : true,
                apiUrl          : 'https://api.flickr.com/services/rest',
                api_key         : '1a566a4170a3b2d920ef82fbd9123627',
                text            : '',
                method          : "flickr.photos.search",
                extras          : "description, date_taken, owner_name, original_format, url_m, url_o, views, originalformat, count_comments, count_faves, realname , tags",
                per_page        : 125,
                content_type    : "json",
                format          : "json",
                nojsoncallback  : 1,
                orientation     : "landscape",
                height          : 1024,
                width           : 1024,
                sort            : "relevance",
                ViewType        : "grid",
                images          : "",
            }
        },
        mounted() {
            EventBus.$on('SearchPhotos', (Query) => {
                this.text = Query;
                this.fetchPhotos(true);
            });
            
            EventBus.$on('SearchImages', () => {
                this.fetchPhotos();
            });

            EventBus.$on('PageUpdated', (PageNumber) => {
                this.page = PageNumber;
                this.fetchPhotos(false);
            });
            
            EventBus.$on('SwitchView', () => {
                if (this.ViewType == "cube") {this.ViewType = "grid";   }
                else{this.ViewType = "cube";}
                this.fetchPhotos();
            });
            
            EventBus.$on('DownloadImage', (imageURL , title) => {
                this.downLoadImage(imageURL , title);
            });
            
            EventBus.$on('JaccardIndexedRecords', () => {
                this.getJaccardIndexedRecords(this.text);
            });
        },
        methods: {
            fetchPhotos: function(reset = false) {
                let _this  = this;
                if (reset) {_this.page  = 1;}
                let params = {
                    api_key         : _this.api_key,
                    text            : _this.text,
                    safe_search     : _this.safe_search,
                    content_type    : _this.content_type,
                    extras          : _this.extras,
                    per_page        : _this.per_page,
                    page            : _this.page,
                    method          : _this.method,
                    format          : _this.format,
                    format          : _this.format,
                    nojsoncallback  : _this.nojsoncallback,
                    sort            : _this.sort,
                    orientation     : _this.orientation,
                    height          : _this.height,
                    width           : _this.width,
                }
                
                _this.loader        = true;
                $.getJSON(_this.apiUrl, params, function(data){
                    _this.loader    = false;
                    _this.FirstLoad = false;
                    _this.images    = data.photos.photo;
                    EventBus.$emit("FirstLoadedUpdated" , _this.FirstLoad , _this.text);
                    EventBus.$emit("Photos" , data.photos);
                });
            },

            getJaccardIndexedRecords: function(text) {
                this.loader = true;
                axios({
                    url          : '/get-jaccard-indexed-records',
                    method       : 'POST',
                    responseType : 'json',
                    data         : { images : this.images , text : this.text },
                }).then((response) => {
                    this.loader = false;
                    EventBus.$emit("JIPhotos" , response.data);
                })
                .catch(function (error) {
                    this.loader = false;
                    console.log(error);
                });
            },
            
            downLoadImage: function(imageURL , title) {
                this.loader = true;
                axios({
                    url          : '/download',
                    method       : 'POST',
                    responseType : 'blob',
                    data         : {imageURL : imageURL},
                }).then((response) => {
                    this.loader = false;
                    const url   = window.URL.createObjectURL(new Blob([response.data]));
                    const link  = document.createElement('a');
                    link.href   = url;
                    link.setAttribute('download', title+'.jpg');
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(function (error) {
                    this.loader = false;
                    console.log(error);
                });
            },
        }
    }
</script>
