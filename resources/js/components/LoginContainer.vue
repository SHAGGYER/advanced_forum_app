<template>
    <div class="container" style="padding: 0">
        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron" style="background-color: #bf130e; padding: 30px 30px;">
                    <h1 style="color: white">Welcome, Guest</h1>
                    <spinner v-if="loading"></spinner>
                    <form v-else v-on:submit.prevent="onSubmit">
                        <div class="alert alert-secondary" v-if="errors.length">
                            <strong>Please correct following errors:</strong>
                            <ul style="margin-bottom: 0">
                                <li v-for="error in errors">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>
                        <div class="form-row align-items-center">
                            <div class="col-sm-4 my-1">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">@</div>
                                    </div>
                                    <input type="text" class="form-control" v-model="email" placeholder="E-mail">
                                </div>
                            </div>
                            <div class="col-sm-4 my-1">
                                <input type="password" class="form-control" v-model="password" placeholder="Password">
                            </div>

                            <div class="col-sm-1 my-1">
                                <button type="submit" class="btn btn-success">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "LoginContainer",
        props: ['app'],
        data()
        {
            return {
                errors: [],
                email: null,
                password: null,
                loading: false,
            }
        },
        methods: {
            onSubmit()
            {
                this.errors = [];

                if (!this.email)
                {
                    this.errors.push('You must enter an email');
                }

                if (!this.password)
                {
                    this.errors.push('You must enter a password');
                }


                if (!this.errors.length)
                {
                    this.loading = true;

                    let data = {
                        email: this.email,
                        password: this.password
                    };

                    this.app.req.post('login', data).then((response) => {

                        this.loading = false;

                        if (response.data.error === 401)
                        {
                            this.password = '';
                            this.errors.push('Could not log you in');
                        }
                        else
                        {
                            this.app.user = response.data;
                            this.app.subscribe();
                        }
                    })
                }
            }
        }
    }
</script>

<style scoped>

</style>