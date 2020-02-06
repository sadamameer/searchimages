<template>
    <div class="container-fluid">
        <div v-if="images.length" class="mt-3">
            <div class="row mt-3 mb-4" v-if="images.length && !FirstLoaded">
                <div class="col">
                    <span>Showing results from <b>{{ (page * per_page) - (per_page - 1) }}-{{ page * per_page }}</b></span>
                </div>
                <div class="col text-center">
                    <input type="checkbox" name="ji" id="ji" @click="getJaccardIndexedRecords()">
                    <label for="ji"><b>View Sorted Images</b></label>
                </div>
                <div class="col text-right">
                    <span><b>{{ total_results }}</b> matching images found</span>
                    <i class="fa fa-object-ungroup ml-3 cursor-pointer" @click="SwitchView()"></i>
                </div>
            </div>
            <div class="row" v-if="images.length">
                <div class="col-md-2 p-0" v-for="image in images" :key="image.id" :id="image.id">
                    <div class="content">
                        <span>
                        <div class="content-overlay"></div>
                        <img class="content-image" :src="image.url_m">
                        <div class="content-details fadeIn-bottom">
                            <div class="mt-2">
                                <a href="javascript:;" @click="show(image)">
                                    <button class="btn btn-outline-light btn-sm">View Details</button>
                                </a>
                            </div>
                        </div>
                        </span>
                    </div>
                </div>
            </div>
            <div class="row mt-4 mb-4" v-if="images.length && !FirstLoaded">
                <div class="col" v-if="PrevPage">
                    <button class="btn btn-outline-success" @click="ChangePageNumber(page-1)">Previous</button>
                </div>
                <div class="col text-right" v-if="NextPage">
                    <button class="btn btn-outline-success" @click="ChangePageNumber(page+1)">Next</button>
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

        show: function(image) {
            this.image = image;
            this.$modal.show('imageDetails');
        },

        hide: function() {
            this.$modal.hide('imageDetails');
        },
        
        downLoadImage: function(imageUrl , title) {
            EventBus.$emit("DownloadImage" , imageUrl , title);
        },

        ChangePageNumber: function(page_number) {
            EventBus.$emit("PageUpdated" , page_number);
        },

        SwitchView: function(page_number) {
            EventBus.$emit("SwitchView");
        }
    },
}
</script>