<template>
    <div>
        <spinner v-if="loading"></spinner>
        <div v-else-if="thread">
            <div class="container" style="padding: 0">
                <div class="jumbotron">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><router-link :to="{ name: 'categories.show' }">Home</router-link></li>
                            <li class="breadcrumb-item"><router-link :to="{ name: 'forum.show', params: { id: thread.forum.id } }">{{ thread.forum.title }}</router-link></li>
                            <li class="breadcrumb-item">{{ thread.title }}</li>
                        </ol>
                    </nav>

                    <h1 class="display-5">{{ thread.title }}</h1>
                </div>
            </div>

            <div class="container" style="padding: 0; margin-bottom: 20px">


                <div class="row">
                    <div class="col-md-12">

                        <!-- Subscription -->
                        <div v-if="app.user" style="float: right; margin-bottom: 20px">
                            <button @click="subscribe" class="btn btn-success" v-if="!subscribeMode">Subscribe</button>
                            <button @click="unsubscribe" class="btn btn-danger" v-else>Unsubscribe</button>
                        </div>

                        <!-- Posts -->

                        <div v-for="(post,index) in thread.posts.data" v-bind:key="index" style="clear: both">
                            <img class="image" :src="baseUrl + '/images/user.png'" style="vertical-align: top;"/>
                            <div class="post-container">
                                <span>{{ post.user.name }}</span>
                                <span style="margin-left: 5px; color: #3d4852;">SAYS:</span>

                                <hr />

                                <p v-html="post.body" style="margin-top: 20px; margin-bottom: 20px"></p>


                                <hr />


                                <p style="margin-top: 10px; margin-bottom: 0; color: #3d4852">
                                    <small>
                                        {{ post.created_at | friendlyDate }}
                                    </small>
                                </p>
                            </div>
                        </div>

                        <!-- Pagination Bottom -->

                        <pagination :total-pages="thread.posts.last_page"
                                    :page="currentPage"
                                    :app="app"
                                    v-show="thread.posts.last_page > 1"
                                    style="margin-top: 20px;"
                                    :on-click-page="clickPage">
                        </pagination>

                        <!-- Reply Button -->

                        <div v-if="app.user">
                            <button v-if="!replyMode"
                                    @click="replyMode=true"
                                    type="button"
                                    class="btn btn-lg btn-success">
                                Reply
                            </button>

                            <button v-else
                                    @click="replyMode=false"
                                    type="button"
                                    class="btn btn-lg btn-danger">
                                Cancel
                            </button>
                        </div>



                        <!-- Reply Form -->

                        <div v-if="replyMode" style="margin-top: 20px">

                            <div v-if="errorBody" class="alert alert-danger" role="alert">
                                {{ errorBody }}
                            </div>

                            <form v-on:submit.prevent="onSubmit">
                                <quill-editor v-model="body"
                                              ref="myQuillEditor"
                                              style="height: 300px; margin-bottom: 60px"
                                              :options="editorOption">
                                </quill-editor>

                                <button class="btn btn-lg btn-success float-right" style="margin-bottom: 50px">Reply</button>

                            </form>
                        </div>
                    </div>

                </div>

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
        name: "thread",
        components: {quillEditor},
        props: ['app'],
        data()
        {
            return {
                threadId: this.app.$route.params.id,
                thread: null,
                baseUrl: BASE_URL,
                replyMode: false,
                body: '',
                currentPage: this.app.$route.query.page ? this.app.$route.query.page : 1,
                errorBody: null,
                editorOption: {
                    placeholder: 'Content',
                    modules: {
                        toolbar: [
                            [{ 'header': 1 }, { 'header': 2 }, {'header' : 3}],
                            ['bold', 'italic'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ],

                    },
                },
                loading: false,
                subscribeMode: false,
            }
        },
        mounted()
        {
            this.getThread();
        },


        filters: {
            friendlyDate(value)
            {
                return moment(value).fromNow();
            }
        },

        watch: {
            '$route.params.id': function (newVal) {
                this.thread = null;
                this.currentPage = 1;
                this.getThread(newVal);

            }

        },

        methods: {

            ratingHasBeenCast(ratings)
            {
                for (let i = 0; i < ratings.length; i ++)
                {
                    if (ratings[i].user_id === this.app.user.id)
                    {
                        return true;
                    }
                }

                return false;
            },

            canCastRating(post)
            {
                if (post.user_id === this.app.user.id)
                {
                    return false;
                }

                return true;
            },

            getThread(newVal)
            {
                this.loading = true;

                let page = 1;
                if (this.currentPage)
                {
                    page = this.currentPage
                }

                if (newVal)
                {
                    this.threadId = newVal;
                }


                this.app.req.get('thread/'+this.threadId+'?page='+page).then((response) => {

                    this.loading = false;

                    if (response.data.id)
                    {
                        this.thread = response.data;
                        this.totalPages = this.thread.posts.last_page;

                        if (this.thread.subscription)
                        {
                            this.subscribeMode = true;
                        }
                    }
                })
            },


            clickPage(page)
            {
                let $this = this;


                this.app.req.get('thread/'+this.threadId+'?page='+page).then(function (response) {

                    $this.thread = response.data;
                    $this.app.$router.replace({name: 'thread.show', query: {page: page}});
                });
            },

            onSubmit()
            {
                let $this = this;

                if (!this.body && this.body.length < 10 )
                {
                    this.errorBody = 'Content must be at least 10 characters long.';
                    this.errors.push(this.errorBody);
                }
                else
                {
                    this.errorBody = null;
                }

                if (!this.errorBody)
                {
                    let data = {
                        body: $this.body,
                        thread_id: $this.threadId,
                    };


                    this.app.req.post('post/create', data).then(function (response) {

                        $this.replyMode = false;
                        $this.body = '';

                        let pageToGoTo = 1;
                        let remainder = $this.thread.posts.total % 10;

                        if (remainder > 0)
                        {
                            pageToGoTo = $this.thread.posts.last_page;
                        }
                        else if (remainder === 0)
                        {
                            pageToGoTo = $this.thread.posts.last_page + 1;
                        }
                        else
                        {
                            pageToGoTo = null;
                        }

                        if (pageToGoTo)
                        {
                            $this.app.$pagination.clickPage(pageToGoTo);
                        }


                    });

                }


            },

            subscribe()
            {
                let data = {
                    thread_id: this.app.$route.params.id
                };

                this.app.req.post('thread/subscribe', data).then((response) => {
                    this.subscribeMode = true;
                })
            },

            unsubscribe()
            {
                let data = {
                    thread_id: this.app.$route.params.id
                };

                this.app.req.post('thread/unsubscribe', data).then((response) => {
                    this.subscribeMode = false;
                })
            },

            increaseRating(post)
            {
                let data = {
                    post_id: post.id
                };


                post.rating++;



                this.app.req.post('post/increase-rating', data).then((response) => {
                    post.ratings.push(response.data);
                })
            },

            decreaseRating(post)
            {
                let data = {
                    post_id: post.id
                };

                post.rating--;


                this.app.req.post('post/decrease-rating', data).then(() => {
                })
            }
        }
    }
</script>

<style>
    p, h1, ol {
        margin-bottom: 0 !important;
    }


    .post-container {
        border: 2px solid #3d4852;
        background-color: #bfbfb3;
        border-radius: 5px;
        margin-bottom: 10px;
        padding: 25px;
        color: white;
        display: inline-block;
        width: calc(100% - 90px);
        margin-left: 10px;
    }

    .post-container::before
    {
        content: ' ';
        position: absolute;
        border: 7px solid transparent;
        left: 90px;
        border-right-color: #3d4852;
    }

    .image
    {
        height: 75px;
    }
</style>