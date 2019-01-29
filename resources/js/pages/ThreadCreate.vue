<template>
    <div>
        <spinner v-if="loading"></spinner>
        <div v-else-if="app.currentForum">
            <div class="container">
                <div class="jumbotron">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'categories.show' }">Home</router-link></li>
                            <li class="breadcrumb-item">
                                <router-link :to="{ name: 'forum.show',
                                                    params: { id: app.currentForum.id } }">
                                    {{ app.currentForum.title }}
                                </router-link>
                            </li>
                            <li class="breadcrumb-item">Create Thread</li>
                        </ol>
                    </nav>

                    <h1 class="display-4">{{ app.currentForum.title }}</h1>
                    <h5>Create Thread</h5>
                </div>
            </div>

            <div class="container">

                <div v-if="errors.length" class="alert alert-danger" role="alert">
                    <strong>Correct following errors:</strong>
                    <ul style="margin-bottom: 0">
                        <li v-for="error in errors">{{ error }}</li>
                    </ul>
                </div>

                <form v-on:submit.prevent="onSubmit">
                    <div class="form-group">
                        <input type="text"
                               v-model="title"
                               class="form-control"
                               v-bind:class="{ 'is-invalid': errorTitle }"
                               placeholder="Title"
                               style="height: 70px; font-size: 40px; padding: 15px"/>
                    </div>
                    <quill-editor v-model="body"
                                  ref="myQuillEditor"
                                  style="height: 500px; margin-bottom: 60px"
                                  :options="editorOption">
                    </quill-editor>

                    <button class="btn btn-lg btn-success float-right" style="margin-bottom: 50px">Create Thread</button>

                </form>
            </div>
        </div>
    </div>
</template>

<script>
    // require styles
    import 'quill/dist/quill.core.css'
    import 'quill/dist/quill.snow.css'
    import 'quill/dist/quill.bubble.css'

    import { quillEditor } from 'vue-quill-editor';



    export default {
        name: "thread-create",
        props: ['app'],
        components: {
            quillEditor
        },
        data()
        {
            return {
                title: '',
                body: '',
                errorTitle: null,
                errorBody: null,
                errors: [],
                editorOption: {
                    placeholder: 'Content',
                    modules: {
                        toolbar: [
                            [{ 'header': 1 }, { 'header': 2 }],
                            ['bold', 'italic'],
                            ['link'],
                            ['blockquote', 'code-block'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ],

                    },
                },
                loading: false,
            }
        },
        mounted()
        {
            if (!this.app.currentForum)
            {
                this.app.$router.push({name: 'categories'});
            }
        },
        methods: {
            onSubmit()
            {
                this.errors = [];


                if (!this.title && this.title.length < 10 )
                {
                    this.errorTitle = 'Title must be at least 10 characters long.';
                    this.errors.push(this.errorTitle);
                }
                else
                {
                    this.errorTitle = null;
                }

                if (!this.body && this.body.length < 10 )
                {
                    this.errorBody = 'Content must be at least 10 characters long.';
                    this.errors.push(this.errorBody);
                }
                else
                {
                    this.errorBody = null;
                }

                if (!this.errors.length)
                {
                    let data = {
                        forum_id: this.app.currentForum.id,
                        title: this.title,
                        body: this.body,
                    };

                    this.loading = true;

                    this.app.req.post('thread/create', data).then((response) => {
                        this.loading = false;

                        if (response.data.id)
                        {
                            this.app.$router.push({name:'thread.show', params: {id: response.data.id}});
                        }
                    })
                }



            }
        }

    }
</script>

<style scoped>
    .jumbotron
    {
        padding: 50px
    }
</style>