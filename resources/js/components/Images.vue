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
                extras          : "description, date_taken, owner_name, original_format, url_m, url_o, views, originalformat, count_comments, count_faves, realname",
                per_page        : 150,
                content_type    : "json",
                format          : "json",
                nojsoncallback  : 1,
                orientation     : "landscape",
                height          : 1024,
                width           : 1024,
                sort            : "relevance",
                ViewType        : "cube",
            }
        },
        mounted() {
            EventBus.$on('SearchPhotos', (Query) => {
                this.text = Query;
                this.fetchPhotos(true);
            });

            EventBus.$on('PageUpdated', (PageNumber) => {
                this.page = PageNumber;
                this.fetchPhotos(false);
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
                
                _this.loader = true;
                $.getJSON(_this.apiUrl, params, function(data){
                _this.loader        = false;
                _this.FirstLoad     = false;
                EventBus.$emit("FirstLoadedUpdated" , _this.FirstLoad , _this.text);
                EventBus.$emit("Photos" , data.photos);
                });
            }
        }
    }
</script>
