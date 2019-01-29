<template>
    <div class="container">
        <div class="row">

            <div class="col-md-6">
                <h1>Forum Installation</h1>

                <hr>

                <div class="alert alert-danger" v-if="errors.length">
                    <strong>Correct following errors before continueing:</strong>
                    <ul style="margin: 0">
                        <li v-for="error in errors">
                            {{ error }}
                        </li>
                    </ul>
                </div>

                <spinner v-if="loading"></spinner>
                <form v-else v-on:submit.prevent="onSubmit">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" v-model="name" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="text" v-model="email" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" v-model="password" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Password Again</label>
                        <input type="password" v-model="passwordAgain" class="form-control">
                    </div>

                    <hr>

                    <div class="form-group">
                        <label>Website Name</label>
                        <input type="text" v-model="websiteName" class="form-control">
                    </div>

                    <button class="btn btn-info">Install</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Install",
        props: ['app'],
        data()
        {
            return {
                email: '',
                name: '',
                password: '',
                passwordAgain: '',
                websiteName: '',
                errors: [],
                loading: false,
            }
        },
        methods: {
            onSubmit()
            {
                this.errors = [];

                if (!this.name)
                {
                    this.errors.push('Name is required.');
                }

                if (!this.app.helper.validateEmail(this.email))
                {
                    this.errors.push('Email must be in correct format.');
                }

                if (!this.password)
                {
                    this.errors.push('Password is required.');
                }

                if (!this.passwordAgain)
                {
                    this.errors.push('Password confirmation is required.');
                }

                if (this.password !== this.passwordAgain)
                {
                    this.errors.push('Passwords must match.');
                }

                if (!this.websiteName)
                {
                    this.errors.push('Website name is required.');
                }

                if (!this.errors.length)
                {
                    let data = {
                        name: this.name,
                        email: this.email,
                        password: this.password,
                        website_name: this.websiteName
                    };

                    this.loading = true;

                    this.app.req.post('install', data).then(() => {
                        this.loading = false;
                        this.app.installed = true;
                        this.app.$router.push({
                            name: 'categories.show'
                        })
                    })
                }
            }
        }
    }
</script>

<style scoped>

</style>