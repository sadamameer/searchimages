<template>
    <div>
        <div v-if="images.length" class="mt-3">
            <div v-if="!FirstLoaded">
                <div class="row mb-5 mr-2 ml-2" v-if="images.length && !FirstLoaded">
                    <div class="col">
                        <span>Showing results from <b>{{ (page * per_page) - (per_page - 1) }}-{{ page * per_page }}</b></span>
                    </div>
                    <div class="col text-right">
                        <span><b>{{ total_results }}</b> matching images found</span>
                        <i class="fa fa-th-large ml-3 cursor-pointer" @click="SwitchView()"></i>
                    </div>
                </div>
                <carousel-3d 
                    :controls-visible="true"
                    :controls-prev-html="'&#10092;'"
                    :controls-next-html="'&#10093;'" 
                    :controls-width="30"
                    :controls-height="60"
                    :autoplay-timeout="1000"
                    :width="450"
                    :height="350"
                >
                    <slide :index="index" v-for="(image , index) in images" :key="image.id">
                        <img :src="image.url_m">
                    </slide>
                </carousel-3d>
                <br>
                <div class="row m-auto" v-if="images.length && !FirstLoaded">
                    <div class="col" v-if="PrevPage">
                        <button class="btn btn-outline-success" @click="ChangePageNumber(page-1)">Previous</button>
                    </div>
                    <div class="col text-right" v-if="NextPage">
                        <button class="btn btn-outline-success" @click="ChangePageNumber(page+1)">Next</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container text-center mt-5 p-5" v-else>
            <img src="404.png" alt="Nothing Found" class="m-auto">
            <br><br>
            <h5>No images found. Please try another query.</h5>
        </div>
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
            images        : []
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
        fetchPhotos: function() {
            EventBus.$emit("SearchPhotos" , this.Query);
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