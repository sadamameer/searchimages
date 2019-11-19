<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="/">Search Images</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent" v-if="!FirstLoaded">
                <ul class="navbar-nav mr-auto"></ul>
                <span class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="text" placeholder="e.g. Mountains" aria-label="Search" v-model="Query">
                <button class="btn btn-outline-success my-2 my-sm-0" type="button" @click="fetchPhotos(true)" :disabled="btnDisabled">Search</button>
                </span>
            </div>
        </nav>
    </div>
</template>

<script>
export default {
    props : ["loader" , "FirstLoad" , "text"],
    data() {
        return {
            loading     : this.loader,
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
    computed: {
        btnDisabled: function () {
            return (this.Query.length) ? false : true;
        }
    }
}
</script>