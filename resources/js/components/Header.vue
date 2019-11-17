<template>
    <div class="cta-section black-trans-bg" v-if="FirstLoaded">
        <div class="container mt-15">
            <div class="row w-75 m-auto">
                <div class="col-md-12">
                    <div class="cta-title text-center">
                        <h1>The internet’s source of freely useable images.</h1>
                    </div>
                </div>
            </div>
            <div class="row w-75 m-auto">
                <div class="col-md-10">
                    <input class="form-control mr-sm-2" type="text" placeholder="e.g. Mountains" aria-label="Search" v-model="Query">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-block my-2 my-sm-0" type="button" @click="fetchPhotos()">Search</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props : ["FirstLoad" , "text"],
    data() {
        return {
            FirstLoaded : this.FirstLoad,
            Query       : this.text,
        }
    },
    mounted() {
        EventBus.$on('FirstLoadedUpdated', (FirstLoadedFlag , SearchQuery) => {
            this.FirstLoaded = FirstLoadedFlag;
            this.Query       = SearchQuery;
        });
    },
    methods: {
        fetchPhotos: function() {
            EventBus.$emit("SearchPhotos" , this.Query);
        },
    },
}
</script>