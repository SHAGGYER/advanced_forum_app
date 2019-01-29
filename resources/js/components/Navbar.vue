<template>
    <div class="container" style="padding: 0; margin-bottom: 20px">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <a class="navbar-brand" href="#">Forum</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <router-link :to="{ name: 'categories.show' }" class="nav-link">Forum <span class="sr-only">(current)</span></router-link>
                    </li>

                    <li class="nav-item dropdown" v-if="app.user">
                        <a @click="hasNewDeactivate" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <span v-if="app.user.has_new_notifications === 1" class="badge badge-pill badge-danger">New</span>
                            Notifications
                        </a>
                        <div class="dropdown-menu">
                            <router-link :to="{ name: 'thread.show', params: { id: notification.thread_id } }"
                                         v-for="(notification, index) in app.user.notifications"
                                         class="dropdown-item"
                                         v-bind:key="index">
                                {{ notification.text }}
                            </router-link>
                            <div v-if="app.user.notifications.length" class="dropdown-divider"></div>
                            <router-link :to="{ name: 'notifications' }" class="dropdown-item">All</router-link>
                        </div>
                    </li>

                    <li class="nav-item" v-if="app.user">
                        <router-link :to="{ name: 'profile.show' }" class="nav-link"><i class="fas fa-user"></i> {{ app.user.name }}</router-link>
                    </li>

                    <li class="nav-item" v-if="app.user">
                        <a class="nav-link" href="javascript:;" @click="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>


                    <li class="nav-item" v-if="!app.user">
                        <a href="javascript:;" @click="openRegistrationModal" class="nav-link">Register</a>
                    </li>
                </ul>
                <form v-on:submit.prevent="search" class="form-inline my-3 my-lg-0">
                    <input v-model="searchQuery" class="form-control mr-sm-4" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                </form>
            </div>
        </nav>

        <register :app="app"></register>
    </div>
</template>

<script>
    import Register from "../pages/Register";
    export default {
        name: "navbar",
        components: {Register},
        props: ['app'],
        data()
        {
            return {
                searchQuery: ''
            }
        },
        mounted()
        {
            this.app.bus.$on('new-notification', (notification) => {
                this.app.user.has_new_notifications = 1;
                this.app.user.notifications.unshift(notification);
                if (this.app.user.notifications.length > 5)
                {
                    this.app.user.notifications.pop();
                }
            });
        },
        methods: {
            logout()
            {
                this.app.unsubscribe();


                this.app.req.post('logout').then(() => {
                    this.app.user = null;
                });
            },

            search()
            {
                if (this.searchQuery)
                    this.app.$router.push({name:'search', query: { query: this.searchQuery }});
            },

            openRegistrationModal()
            {
                $('#registration-modal').modal({
                    backdrop: 'static',
                    keyboard: false,
                })
            },

            hasNewDeactivate()
            {
                if (!this.app.user.has_new_notifications)
                {
                    return;
                }

                this.app.user.has_new_notifications = 0;

                this.app.req.post('notification/has-new-deactivate').then(() => {

                });
            }
        }
    }
</script>

<style scoped>

</style>