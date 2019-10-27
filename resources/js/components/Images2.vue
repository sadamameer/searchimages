<template>
    <div>
        <div v-if="loader" id="loader_transaparent"></div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="/">Search Images</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto"></ul>
                <span class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="text" placeholder="e.g. Mountains" aria-label="Search" v-model="query">
                <button class="btn btn-outline-success my-2 my-sm-0" type="button" @click="fetchPhotos()">Search</button>
                </span>
            </div>
        </nav>
        <div class="container-fluid">
            <div class="row mt-3" v-if="images.length">
                <div class="col">
                    <span>Showing results from <b>{{ (page * per_page) - (per_page - 1) }}-{{ page * per_page }}</b></span>
                </div>
                <div class="col text-right">
                    <span><b>{{ total }}</b> matching images found</span>
                </div>
            </div>
            <div class="row">
                <div class="gallery" v-if="images.length">
                    <figure @click="open(image.id)" v-for="image in images" :key="image.id" :id="image.id">
                        <img :class="image.id" :src="image.webformatURL" alt="" />
                        <figcaption>{{ image.user.toUpperCase() }} <small>{{ image.imageWidth }} x {{ image.imageHeight }}</small></figcaption>
                    </figure>
                </div>
            </div>
            <div class="row mb-3" v-if="images.length">
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
                isOpen      : false,
                loader      : false,
                apiUrl      : 'https://pixabay.com/api',
                apiKey      : '13952953-8fa583f384953ec04b9327ba3',
                images      : [],
                query       : '',
                imageType   : 'photo',
                orientation : 'horizontal',
                safesearch  : true,
                order       : "popular",
                page        : 1,
                per_page    : 150,
                total       : 0,
                NextPage    : false,
                PrevPage    : false,
            }
        },
        mounted() {
            this.fetchPhotos();
            $(document).on('click', '.popup img', function(){
                return false;
            }).on('click', '.popup', function(){
                $('.gallery, .popup').removeClass('pop');
                setTimeout(function() {
                    $('.popup').remove()
                }, 100);
            })
        },
        watch: {
            images: function (val) {
                this.NextPage = false;
                this.PrevPage = false;

                console.log(this.images.length)
                console.log(this.per_page * this.page)
                console.log(this.total)

                if (this.images.length && (this.per_page * this.page < this.total)) {
                    this.NextPage = true;
                }

                if (this.images.length && (((this.page * this.per_page) - this.per_page) > 0)) {
                    this.PrevPage = true;
                }

                console.log(this.NextPage);
                console.log(this.PrevPage);
            },
        },
        methods: {
            ChangePageNumber: function(page_number) {
                this.page = page_number;
                this.fetchPhotos();
            },

            fetchPhotos: function() {
                let _this = this;

                let search = _this.query;
                if (!search) { search = "nature"}

                let params = {
                    "key"          : _this.apiKey,
                    "q"            : encodeURIComponent(_this.query),
                    "image_type"   : _this.imageType,
                    "orientation"  : _this.orientation,
                    "safesearch"   : _this.safesearch,
                    "order"        : _this.order,
                    "page"         : _this.page,
                    "per_page"     : _this.per_page
                }
                
                _this.loader = true;
                $.getJSON(_this.apiUrl, params, function(data){
                _this.loader = false;
                if (parseInt(data.totalHits) > 0){
                    _this.images = data.hits;
                    _this.total  = data.totalHits;
                }
                else
                    console.log('No hits');
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
            },
        }
    }
</script>
