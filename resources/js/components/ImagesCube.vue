<template>
    <div>
        <div v-if="images.length" class="mt-3">
            <div v-if="!FirstLoaded">
                <div class="row mb-5 mr-2 ml-2" v-if="images.length && !FirstLoaded">
                    <div class="col">
                        <span>Showing results from <b>{{ (page * per_page) - (per_page - 1) }}-{{ page * per_page }}</b></span>
                    </div>
                    <div class="col text-center">
                        <input type="checkbox" name="ji" id="ji" @click="getJaccardIndexedRecords()">
                        <label for="ji"><b>View Sorted Images</b></label>
                    </div>
                    <div class="col text-right">
                        <span><b>{{ total_results }}</b> matching images found</span>
                        <i class="fa fa-th-large ml-3 cursor-pointer" @click="SwitchView()"></i>
                    </div>
                </div>
                <div id="container" class="w-100">
                    <div class="row m-auto">
                        <div class="col-md-8">
                            <div class="row box m-auto" :id="`${'box'+x}`" :style="`${'z-index: '+x+'; top: '+(x * margin)+'px; left: '+(x * margin)+'px;'}`" v-for="(x , index) in slidesArray" :key="index+1">
                                <div class="cell" v-for="image in getImages(index+1)" :key="image">
                                    <a href="javascript:;" @click="show(image)">
                                        <img :src="image.url_m" alt="bull" class="w-100 h-100">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col text-center mt-5">
                            <h1>Switch Planes</h1>
                            <hr>
                            <div class="row m-auto" v-if="images.length && !FirstLoaded">
                                <div class="col-lg-12 mb-1" v-for="(x , index) in slidesArray" :key="index+1">
                                    <button class="btn btn-block btn-primary" v-if="currentActive == x" @click="toggleSlide(x)">Show Plane {{ index+1 }}</button>
                                    <button class="btn btn-block btn-outline-primary" v-else @click="toggleSlide(x)">Show Plane {{ index+1 }}</button>
                                </div>
                            </div>
                            <hr>
                            <div class="row m-auto" v-if="images.length && !FirstLoaded">
                                <div class="col-lg-12 mb-1" v-if="PrevPage">
                                    <button class="btn btn-block btn-outline-success" @click="ChangePageNumber(page-1)">Previous</button>
                                </div>
                                <div class="col-lg-12" v-if="NextPage">
                                    <button class="btn btn-block btn-outline-success" @click="ChangePageNumber(page+1)">Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container text-center mt-5 p-5" v-else>
            <img src="404.png" alt="Nothing Found" class="m-auto">
            <br><br>
            <h5>No images found. Please try another query.</h5>
        </div>
        <modal name="imageDetails" :width="1000" :height="320">
            <div class="row m-auto">
                <div class="col p-0">
                    <img :src="image.url_m" alt="bull" class="w-100 h-100">
                </div>
                <div class="col">
                    <div class="content-details content-right fadeIn-bottom">
                        <h3 class="content-title">{{image.ownername}}</h3>
                        <div class="d-inline-flex">
                            <span><i class="fa fa-heart text-danger"></i><p>{{image.count_faves}}</p></span>
                            <span><i class="fa fa-comments text-info"></i><p>{{image.count_comments}}</p></span>
                            <span><i class="fa fa-eye text-warning"></i><p>{{image.views}}</p></span>
                        </div>
                        <div class="mt-2">
                            <a href="javascript:;" @click="downLoadImage(image.url_o , image.title)">
                                <button class="btn btn-outline-dark btn-sm">Download</button>
                            </a>
                        </div>
                        <div class="mt-3">
                            <p class="content-text mb-0">{{ image.datetaken }}</p>
                            <span class="badge badge-success" v-if="image.originalformat">{{ image.originalformat }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
import { timeout } from 'q';
export default {
    props : ["PerPage"],
    data() {
        return {
            FirstLoaded   : true,
            PrevPage      : false,
            NextPage      : false,
            page          : 1,
            per_page      : this.PerPage,
            total_results : 0,
            total_pages   : 0,
            slides        : 0,
            slidesArray   : [],
            currentActive : 0,
            margin        : 40,
            per_slide     : 25,
            images        : [],
            image         : "",
        }
    },
    mounted() {
        EventBus.$on('FirstLoadedUpdated', (FirstLoadedFlag , SearchQuery) => {
            this.FirstLoaded = FirstLoadedFlag;
        });

        EventBus.$on('Photos', (Photos) => {
            this.images        = Photos.photo;
            this.page          = Photos.page;
            this.total_pages   = Photos.pages;
            this.per_page      = Photos.perpage;
            this.total_results = Photos.total;
            $( "#ji" ).prop( "checked" , false );
            this.slides        = this.per_page / this.per_slide;
            this.getSlidesArray();
            this.currentActive = this.slides;
            setTimeout(() => {this.toggleSlide(this.slides);}, 500);
        });

        EventBus.$on('JIPhotos', (Photos) => {
            this.images        = Photos;
        });
    },
    watch: {
        images: function (val) {
            this.NextPage = false;
            this.PrevPage = false;

            if (this.total_results && (this.per_page * this.page < this.total_results)) {
                this.NextPage = true;
            }

            if (this.total_results && this.page > 1) {
                this.PrevPage = true;
            }
        },
    },
    methods: {
        getJaccardIndexedRecords: function() {
            if ($('#ji').is(':checked')) {
                EventBus.$emit("JaccardIndexedRecords");
            }
            else{
                EventBus.$emit("SearchImages");
            }
        },

        downLoadImage: function(imageUrl , title) {
            EventBus.$emit("DownloadImage" , imageUrl , title);
        },

        getSlidesArray: function() {
            this.slidesArray = [];
            for (let index = 0; index < this.slides; index++) {this.slidesArray.push(index + 1);}
            this.slidesArray = this.slidesArray.slice().reverse();
        },
        
        show: function(image) {
            this.image = image;
            this.$modal.show('imageDetails');
        },

        hide: function() {
            this.$modal.hide('imageDetails');
        },

        ChangePageNumber: function(page_number) {
            EventBus.$emit("PageUpdated" , page_number);
        },

        SwitchView: function(page_number) {
            EventBus.$emit("SwitchView");
        },
        
        toggleSlide: function(slide) {
            this.currentActive = slide;
            for (let index = 1; index <= this.slides; index++) {
                $("#box"+index).css("z-index" , index);
                $("#box"+index).css("transform" , "scale(1)");
                $("#box"+index).css("transition" , "transform 1s");
                $("#box"+index).addClass("dull");
            }
            $("#box"+slide).css("z-index" , (this.slides + 1));
            $("#box"+slide).css("transform" , "scale(1.05)");
            $("#box"+slide).css("transition" , "transform 1s");
            $("#box"+slide).removeClass("dull");
        },
        
        getImages: function(x) {
            x = x - 1;
            let chunk = [];

            for (let index = (x * this.per_slide); index < ((x * this.per_slide) + this.per_slide); index++) {
                chunk.push(this.images[index]);
            }

            return chunk;
        }
    },
}
</script>