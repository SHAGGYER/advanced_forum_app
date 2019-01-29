<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Create Forum for {{ app.currentCategory ? app.currentCategory.title : '' }}</h1>

                <div class="alert alert-info">
                    Here you can create a forum.
                </div>

                <spinner v-if="loading"></spinner>

                <form v-else v-on:submit.prevent="onSubmit">
                    <div v-if="errors.length" class="alert alert-danger">
                        <ul style="margin-bottom: 0">
                            <li v-for="error in errors">
                                {{ error }}
                            </li>
                        </ul>
                    </div>


                    <div class="form-group">
                        <label>Title</label>
                        <input class="form-control" type="text" v-model="title" placeholder="Title">
                    </div>

                    <button type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ForumCreate",
        props: ['app'],
        data()
        {
            return {
                title: '',
                loading: false,
                errors: []
            }
        },
        created()
        {
            if (!this.app.currentCategory)
            {
                this.app.$router.push({
                    name: 'categories.show'
                })
            }
        },
        methods: {
            onSubmit()
            {
                this.errors = [];

                if (!this.title)
                {
                    this.errors.push('You must specify a title.');
                }

                if (!this.errors.length)
                {
                    this.loading = true;

                    let data = {
                        title: this.title,
                        category_id: this.app.currentCategory.id
                    };

                    this.app.req.post('forum/create', data).then(() => {
                        this.loading = false;
                        this.app.$router.push({
                            name: 'categories.show'
                        })
                    })
                }
            }
        },

    }
</script>

<style scoped>

</style>