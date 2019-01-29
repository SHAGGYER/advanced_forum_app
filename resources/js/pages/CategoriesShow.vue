<template>
    <div class="container" style="padding: 0">
        <button @click="goToCreate" class="btn btn-success" v-if="app.user && app.user.admin === 1" style="margin-bottom: 20px">Create Category</button>

        <spinner v-if="loading"></spinner>
        <div v-else>

            <div class="container" v-for="category in categories" v-bind:key="category.id" style="margin-bottom: 13px">

                <div class="row justify-content-center">
                    <div class="col-md-12" style="padding: 0">
                        <div class="card card-default">
                            <div class="card-header">


                                {{ category.title }}

                                <div class="float-right" v-if="app.user && app.user.admin === 1">
                                    <div class="dropdown">
                                        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                            Actions
                                        </a>

                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:;"><i class="far fa-edit"></i> Edit Category</a>
                                            <a @click="createForum(category)" class="dropdown-item" href="javascript:;"><i class="fas fa-plus-circle"></i> Create Forum</a>
                                            <a @click="openDeleteCategoryConfirmationDialog(category)"
                                               class="dropdown-item"
                                               href="javascript:;">
                                                <i class="far fa-trash-alt"></i> Delete Category
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex align-content-center"
                                    style="height: 100px"
                                    v-for="forum in category.fora"
                                    v-bind:key="forum.id">
                                    <div style="width: 40%; display: inline-block;">
                                        <h5 style="line-height: 75px;">
                                            <router-link :to="{ name: 'forum.show', params: { id: forum.id } }">
                                                {{ forum.title }}
                                            </router-link>
                                        </h5>
                                    </div>
                                    <div style="width: 20%; display: inline-block; text-align: center">
                                            <span style="line-height: 75px;">
                                                {{ forum.views }} Views / <strong>{{ forum.replies ? forum.replies - 1 : 0 }} Replies</strong>
                                            </span>
                                    </div>
                                    <div style="width: 40%; display: inline-block; padding-top: 15px" v-if="forum.latest">
                                        <router-link :to="{ name: 'thread.show', params: { id: forum.latest.thread_id } }">
                                            {{ forum.latest.thread_title }}

                                        </router-link>
                                        <br/>
                                        <span>by {{ forum.latest.user.name }} &middot; {{ forum.latest.created_at | friendlyDate }}</span>
                                    </div>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <confirmation-dialog v-bind:onYes="deleteCategory" id="delete-category-dialog">
            <template slot="content">
                <div class="alert alert-danger" role="alert">
                    Category will be deleted, and all of its fora, threads and posts. Are you sure?
                </div>
            </template>
        </confirmation-dialog>
    </div>
</template>

<script>
    import ConfirmationDialog from '../components/confirmation-dialog.vue';

    export default {


        name: "categories",
        props: ['app'],
        components: {
            ConfirmationDialog,
        },
        data()
        {
            return {
                categories: null,
                loading: false,
                currentCategory: null,
            }
        },


        filters: {
            friendlyDate(value)
            {
                return moment(value).fromNow();
            }
        },

        mounted()
        {
            this.getCategories();
        },
        methods:
            {
                getCategories()
                {
                    let $this = this;

                    this.loading = true;

                    this.app.req.get('categories').then(function (response) {
                        $this.loading = false;
                        $this.categories = response.data;
                    })
                },

                goToCreate()
                {
                    this.app.$router.push({
                        name: 'categories.create'
                    });
                },

                openDeleteCategoryConfirmationDialog(category)
                {
                    this.currentCategory = category;

                    $('#delete-category-dialog').modal({
                        keyboard: false,
                        backdrop: 'static',
                    });
                },

                deleteCategory(onFinish)
                {
                    let data = {
                        category_id: this.currentCategory.id
                    };

                    this.app.req.post(BASE_URL + '/categories/delete', data).then(() => {
                        let index = this.categories.indexOf(this.currentCategory);
                        this.categories.splice(index, 1);


                        onFinish();
                    })
                },


                createForum(category)
                {
                    this.app.currentCategory = category;
                    this.app.$router.push({
                        name: 'forum.create'
                    });
                }
            }
    }
</script>

<style scoped>

</style>