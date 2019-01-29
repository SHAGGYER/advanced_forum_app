<template>
    <div class="modal" tabindex="-1" role="dialog" id="registration-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Register
                    </h5>
                    <button type="button" class="close" @click="close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <spinner v-if="loading"></spinner>
                    <form v-else v-on:submit.prevent="onSubmit">
                        <div v-if="errors.length" class="alert alert-danger">
                            <strong>Please correct:</strong>
                            <ul>
                                <li v-for="error in errors">{{ error }}</li>
                            </ul>
                        </div>

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

                        <button type="submit" class="btn btn-success">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "register",
        props: ['app'],
        data()
        {
            return {
                name: '',
                email: '',
                password: '',
                passwordAgain: '',
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

                if (!this.errors.length)
                {
                    let data = {
                        name: this.name,
                        email: this.email,
                        password: this.password,
                    };

                    this.loading = true;

                    this.app.req.post('register', data).then((response) => {
                        this.loading = false;
                        this.app.user = response.data;
                        this.close();
                        this.app.$router.push({
                            name: 'categories.show'
                        })
                    })
                }
            },

            close()
            {
                $('#registration-modal').modal('hide');
            }
        }
    }
</script>

<style scoped>

</style>