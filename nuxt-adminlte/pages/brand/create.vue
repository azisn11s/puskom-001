<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Add Brand</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">New Brand</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="create" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Brand</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.brand_name"
										type="text"
										class="form-control"
										id="brand_name"
										placeholder="Brand name" 
										:class="{ 'is-invalid': form.errors.has('brand_name') }"
									/>
									<has-error :form="form" field="brand_name"></has-error>
								</div>
							</div>
							
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<my-button :loading="isLoading">
								Submit
							</my-button>

							<nuxt-link tag="a" to="/brands" class="btn btn-default float-right">
								Cancel
							</nuxt-link>
						</div>
						<!-- /.card-footer -->
					</form>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import Form from "vform";

export default {
	data(){
		return {
			form: new Form({
				brand_name: '',
			}),
			isLoading: false
		}
	},

	methods: {
		async create(){
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {

				await this.$axios.post(`brands`, this.form);

				this.$nuxt.$loading.finish();
				this.isLoading = false;

				this.$router.push(`/brands`);
			} catch (error) {
				console.log('ERROR!!', error);

				if (error.response && error.response.status == 422) {
					this.$nextTick(() => {
						this.form.errors.set(error.response.data.errors);
					});

					this.$toast.error(`${error.response.data.message}`, {
						icon: "exclamation-triangle",
						iconPack: "fontawesome",
						duration: 5000,
					});	

				} else if(error.response && error.response.status == 403) {
					let message = "Action failed.";

					if (error.response.data) {
						message = error.response.data.message;
					}

					this.$toast.error(`${message}`, {
						icon: "exclamation-triangle",
						iconPack: "fontawesome",
						duration: 4000,
					});	
				} else {
					this.$toast.error(`Error on submitting form!.`, {
						icon: "exclamation-triangle",
						iconPack: "fontawesome",
						duration: 5000,
					});	
				}	

				this.isLoading = false;
				this.$nuxt.$loading.finish();

			}
		}
	}
};
</script>

<style>
</style>