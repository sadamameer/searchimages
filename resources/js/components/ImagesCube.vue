<template>
    <div v-if="!FirstLoaded">
        <div v-if="images.length">
            <carousel-3d 
                :controls-visible="true"
                :controls-prev-html="'&#10092;'"
                :controls-next-html="'&#10093;'" 
                :controls-width="30"
                :controls-height="60"
                :autoplay-timeout="1000"
                :width="400"
                :height="250"
            >
                <slide :index="index" v-for="(image , index) in images" :key="image.id">
                    <img :src="image.url_m">
                </slide>
            </carousel-3d>
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
    },
}
</script>