<template>
    <div>
        <div v-if="loader" id="loader_transaparent"></div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="/">Search Images</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent" v-if="!FirstLoad">
                <ul class="navbar-nav mr-auto"></ul>
                <span class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="text" placeholder="e.g. Mountains" aria-label="Search" v-model="text">
                <button class="btn btn-outline-success my-2 my-sm-0" type="button" @click="fetchPhotos(true)">Search</button>
                </span>
            </div>
        </nav>
        <div class="cta-section black-trans-bg" v-if="FirstLoad">
            <div class="container">
                <div class="row w-75 m-auto">
                    <div class="col-md-12">
                        <div class="cta-title text-center">
                            <h1>The internet’s source of freely useable images.</h1>
                        </div>
                    </div>
                </div>
                <div class="row w-75 m-auto">
                    <div class="col-md-10">
                        <input class="form-control mr-sm-2" type="text" placeholder="e.g. Mountains" aria-label="Search" v-model="text">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success btn-block my-2 my-sm-0" type="button" @click="fetchPhotos(true)">Search</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row mt-4 mb-4" v-if="images.length && !FirstLoad">
                <div class="col">
                    <span>Showing results from <b>{{ (page * per_page) - (per_page - 1) }}-{{ page * per_page }}</b></span>
                </div>
                <div class="col text-right">
                    <span><b>{{ total_results }}</b> matching images found</span>
                </div>
            </div>
            <div class="row" v-if="images.length">
                <div class="col-md-4 p-0" v-for="image in images" :key="image.id" :id="image.id">
                    <div class="content">
                        <span>
                        <div class="content-overlay"></div>
                        <img class="content-image" :src="image.url_m">
                        <div class="content-details fadeIn-bottom">
                            <h3 class="content-title">{{image.ownername.toUpperCase()}}</h3>
                            <div class="d-inline-flex">
                                <span><i class="fa fa-heart"></i><p>{{image.count_faves}}</p></span>
                                <span><i class="fa fa-comments"></i><p>{{image.count_comments}}</p></span>
                                <span><i class="fa fa-eye"></i><p>{{image.views}}</p></span>
                            </div>
                            <div class="mt-2">
                                <a :href="image.url_o" target="_blank"><button class="btn btn-outline-light btn-sm">View Original</button></a>
                            </div>
                            <div class="mt-3">
                                <p class="content-text mb-0">{{ image.datetaken }}</p>
                                <span class="badge badge-success" v-if="image.originalformat">{{ image.originalformat.toUpperCase() }}</span>
                            </div>
                        </div>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row mt-4 mb-4" v-if="images.length && !FirstLoad">
                <div class="col" v-if="PrevPage">
                    <button class="btn btn-outline-success" @click="ChangePageNumber(page-1)">Previous</button>
                </div>
                <div class="col text-right" v-if="NextPage">
                    <button class="btn btn-outline-success" @click="ChangePageNumber(page+1)">Next</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                isOpen          : false,
                loader          : false,
                FirstLoad       : true,
                
                apiUrl          : 'https://api.flickr.com/services/rest',
                api_key         : '1a566a4170a3b2d920ef82fbd9123627',
                text            : '',
                method          : "flickr.photos.search",
                extras          : "description, date_taken, owner_name, original_format, url_m, url_o, views, originalformat, count_comments, count_faves, realname",
                per_page        : 150,
                page            : 1,
                content_type    : "json",
                format          : "json",
                nojsoncallback  : 1,
                orientation     : "landscape",
                height          : 1024,
                width           : 1024,
                sort            : "relevance",

                images          : [],
                total_results   : 0,
                total_pages     : 0,
                NextPage        : false,
                PrevPage        : false,
            }
        },
        mounted() {
            this.fetchDefaultPhotos(false);
        },
        watch: {
            images: function (val) {
                this.NextPage = false;
                this.PrevPage = false;

                console.log(this.images.length)
                console.log(this.per_page * this.page)
                console.log(this.total)

                if (this.total_results && (this.per_page * this.page < this.total_results)) {
                    this.NextPage = true;
                }

                if (this.total_results && this.page > 1) {
                    this.PrevPage = true;
                }

                console.log(this.NextPage);
                console.log(this.PrevPage);
            },
        },
        methods: {
            ChangePageNumber: function(page_number) {
                this.page = page_number;
                this.fetchPhotos(false);
            },

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
                _this.loader = false;
                if (data.photos.total){
                    _this.images        = data.photos.photo;
                    _this.total_results = data.photos.total;
                    _this.FirstLoad     = false;
                }
                else
                    _this.images        = [];
                });
            },
            
            fetchDefaultPhotos: function(reset = false) {
                let _this  = this;
                if (reset) {_this.page  = 1;}
                let params = {
                    api_key         : _this.api_key,
                    text            : _this.text,
                    safe_search     : _this.safe_search,
                    content_type    : _this.content_type,
                    extras          : _this.extras,
                    per_page        : 60,
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
                _this.loader = false;
                if (data.photos.total){
                    _this.images        = data.photos.photo;
                    _this.total_results = data.photos.total;
                }
                else
                    _this.images        = [];
                });
            },

            open: function(imageID) {
                let $figure = $("#"+imageID);
                $('.gallery').addClass('pop');
                let $popup = $('<div class="popup" />').appendTo($('body'));
                let $fig = $figure.clone().appendTo($('.popup'));
                let $bg = $('<div class="bg" />').appendTo($('.popup'));
                let $close = $('<div class="close">x</div>').appendTo($fig);
                let $shadow = $('<div class="shadow" />').appendTo($fig);
                let src = $('img', $fig).attr('src');
                $shadow.css({
                    backgroundImage: 'url(' + src + ')'
                });
                $bg.css({
                    backgroundImage: 'url(' + src + ')'
                });
                setTimeout(function() {
                    $('.popup').addClass('pop');
                    this.isOpen = true;
                }, 10);
            }
        }
    }
</script>
