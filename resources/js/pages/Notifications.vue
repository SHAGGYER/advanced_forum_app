<template>
    <div class="container" style="padding: 0;">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Notifications</div>
                    <div class="card-body">
                        <spinner v-if="loading"></spinner>
                        <div v-else-if="notifications.length" class="list-group list-group-flush">
                            <router-link class="list-group-item
                                         d-flex
                                         list-group-item-action
                                         justify-content-between
                                         align-items-center"
                                 style="padding: 25px"
                                 :to="{ name: 'thread.show', params: { id: notification.thread_id } }"
                                 v-for="notification in notifications"
                                 v-bind:key="notification.id">
                                {{ notification.text }}
                            </router-link>
                        </div>
                        <div v-else class="alert alert-secondary">
                            No notifications exist.
                        </div>

                        <pagination v-if="notifications.length"
                                    :total-pages="lastPage"
                                    :page="currentPage"
                                    style="margin-top: 20px"
                                    :app="app"
                                    :on-click-page="clickPage">
                        </pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Notifications",
        props: ['app'],
        data()
        {
            return {
                notifications: [],
                loading: false,
                lastPage: 0,
                currentPage: this.app.$route.query.page ? this.app.$route.query.page : 1,
            }
        },
        mounted() {
            this.getNotifications();
        },
        methods: {
            getNotifications()
            {
                this.loading = true;

                this.app.req.get('notification/get?page='+this.currentPage).then((response) => {
                    this.loading = false;
                    if (response.data.data)
                    {
                        this.notifications = response.data.data;
                        this.lastPage = response.data.last_page;
                    }

                });
            },
            clickPage(page)
            {
                this.loading = true;


                this.app.req.get('notification/get?page='+parseInt(page)).then((response) => {
                    this.loading = false;

                    if (response.data.data)
                    {
                        this.notifications = response.data.data;
                        this.app.$router.replace({
                            name: 'notifications',
                            query: {
                                page: parseInt(page)
                            }
                        })
                    }

                });
            },
        }
    }
</script>

<style scoped>

</style>