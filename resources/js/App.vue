<template>
    <div>
        <navbar :app="this" v-if="installed"></navbar>

        <spinner v-if="loading" style="margin-top: 20px"></spinner>
        <div v-else-if="initiated">
            <LoginContainer v-if="!user && installed" :app="this" style="margin-top: 20px"></LoginContainer>
            <router-view :app="this"></router-view>
        </div>
    </div>
</template>

<script>
    import Helper from './utils/helper';
    import Navbar from "./components/Navbar";
    import LoginContainer from "./components/LoginContainer";
    export default {
        name: "app",
        components: {LoginContainer, Navbar},
        data() {
            return {
                user: null,
                initiated: false,
                loading: false,
                req: axios.create({
                    baseURL: BASE_URL
                }),
                helper: Helper,
                currentCategory: null,
                currentForum: null,
                installed: false,
                bus: new Vue()
            }
        },
        mounted() {
            this.init();

        },
        methods: {
            subscribe()
            {
                let channel = pusher.subscribe(`private-user.${this.user.id}`);
                channel.bind('NotificationSent', (data) => {
                    this.bus.$emit('new-notification', data.notification);
                });
            },

            unsubscribe()
            {
                pusher.unsubscribe(`private-user.${this.user.id}`);
            },

            init() {
                this.loading = true;

                this.req.get('init').then((response) => {
                    this.initiated = true;
                    this.loading = false;
                    this.installed = response.data.installed;
                    this.user = response.data.user;





                    if (!this.installed)
                    {
                        this.$router.push({
                            name: 'install'
                        });
                    }

                    this.$router.beforeEach((to, from, next) => {
                        if (!this.installed && to.name !== 'install')
                        {
                            next('/install');
                        }
                        else if (this.installed && to.name === 'install')
                        {
                            next('/');
                        }
                        else
                        {
                            next();
                        }
                    });

                    if (this.user)
                    {
                        this.subscribe();
                    }


                });
            }
        }
    }
</script>

<style scoped>

</style>