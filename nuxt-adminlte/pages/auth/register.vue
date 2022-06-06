<template>
	<div class="register-box">
		<div class="card card-outline card-primary">
			<div class="card-header text-center">
				<img
					src="~static/upcore/icons/UPCORE1024px.png"
					alt="UpCore001 Logo"
					class="brand-image"
					style="opacity: 0.8; width: 70%;"
				/>
			</div>
			<div class="card-body">
				<p class="login-box-msg">Register a new membership</p>

				<form @submit.prevent="userRegister">
					<div class="input-group mb-3">
						<input
							type="text"
							class="form-control"
							placeholder="Username"
							v-model="register.username"
							:class="{ 'is-invalid': register.errors.has('username') }"
						/>
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-user"></span>
							</div>
						</div>
						<has-error :form="register" field="username"></has-error>
					</div>
					<div class="input-group mb-3">
						<input
							type="email"
							class="form-control"
							placeholder="Email"
							v-model="register.email"
							:class="{ 'is-invalid': register.errors.has('email') }"
						/>
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-envelope"></span>
							</div>
						</div>
						<has-error :form="register" field="email"></has-error>
					</div>
					<div class="input-group mb-3">
						<input
							type="password"
							class="form-control"
							placeholder="Password"
							v-model="register.password"
							:class="{ 'is-invalid': register.errors.has('password') }"
						/>
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
						<has-error :form="register" field="password"></has-error>
					</div>
					<div class="input-group mb-3">
						<input
							type="password"
							class="form-control"
							placeholder="Retype password"
							v-model="register.password_confirmation"
						/>
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-8">
							<!-- <div class="icheck-primary">
								<input
									type="checkbox"
									id="agreeTerms"
									name="terms"
									value="agree"
								/>
								<label for="agreeTerms">
									I agree to the <a href="#">terms</a>
								</label>
							</div> -->
						</div>
						<!-- /.col -->
						<div class="col-4">
							<button type="submit" class="btn btn-primary btn-block">
								Register
							</button>
						</div>
						<!-- /.col -->
					</div>
				</form>

				<div class="social-auth-links text-center">
					<!-- <a href="#" class="btn btn-block btn-primary">
						<i class="fab fa-facebook mr-2"></i>
						Sign up using Facebook
					</a>
					<a href="#" class="btn btn-block btn-danger">
						<i class="fab fa-google-plus mr-2"></i>
						Sign up using Google+
					</a> -->
				</div>

				<router-link to="/login" class="text-center">I already have a membership</router-link>

			</div>
			<!-- /.form-box -->
		</div>
		<!-- /.card -->
	</div>
	<!-- /.register-box -->
</template>

<script>
import {Form} from "vform";

export default {
    layout: "guest",
    head: {
		bodyAttrs: {
			class: "hold-transition register-page",
            style: ""
		},
	},
	data() {
		return {
			register: new Form({
				username: "",
				email: "",
				password: "",
				password_confirmation: "",
			}),
		};
	},
	methods: {
		async userRegister() {
			try {
                let response = await this.$axios.post(`/signup`, this.register);

				this.$auth.setUserToken(response.data.access_token).then(()=> {

					this.$toast.success(`Successfully registered. Please login!`, {
						icon: "check",
						iconPack: "fontawesome",
						duration: 5000,
						theme: 'outline',
						position: 'top-center',
						action : {
							text : 'Close',
							onClick : (e, toastObject) => {
								toastObject.goAway(0);
							}
						},
					});		
					
					this.$router.push({ path: '/' });
				});              
				

			} catch (error) {
                
                if (error.response && error.response.status == 422) {
					this.$nextTick(() => {
						this.register.errors.set(error.response.data.errors);
					});
				}

				this.$toast.error(`Unauthenticated.`, {
					icon: "exclamation-triangle",
					iconPack: "fontawesome",
					duration: 5000,
				});	
			}

			
		},
	},
};
</script>

<style>
</style>