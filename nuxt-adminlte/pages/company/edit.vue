<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Edit Company - #{{ form.id }}</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">Company</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="edit" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Company</label>
								<div class="col-sm-8">
									<input
										v-model="form.name"
										type="text"
										class="form-control"
										id="name"
										placeholder="Company Name"
										:class="{ 'is-invalid': form.errors.has('name') }"
									/>
									<has-error :form="form" field="name"></has-error>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Register Code</label>
								<div class="col-sm-8">
									<input
										v-model="company.register_code"
										type="text"
										class="form-control form-control-border border-width-2"
										id="register_code"
										disabled
									/>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Address</label>
								<div class="col-sm-8">
									<textarea
										class="form-control"
										v-model="form.full_address"
										placeholder="Full Address"
									></textarea>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Email</label>
								<div class="col-sm-8">
									<input
										v-model="form.official_email_address"
										type="text"
										class="form-control"
										placeholder="Company Email Address"
										:class="{
											'is-invalid': form.errors.has(
												'official_email_address'
											),
										}"
									/>
									<has-error
										:form="form"
										field="official_email_address"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Phone</label>
								<div class="col-sm-8">
									<input
										v-model="form.phone_number"
										type="text"
										class="form-control"
										placeholder="Phone Number"
										:class="{
											'is-invalid': form.errors.has('phone_number'),
										}"
									/>
									<has-error
										:form="form"
										field="phone_number"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Zip Code</label>
								<div class="col-sm-4">
									<input
										v-model="form.zip_code"
										type="text"
										class="form-control"
										placeholder="Zip Code or Postal Code"
										:class="{
											'is-invalid': form.errors.has('zip_code'),
										}"
									/>
									<has-error :form="form" field="zip_code"></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<my-button :loading="isLoading">Update</my-button>

							<nuxt-link
								tag="a"
								to="/companies"
								class="btn btn-default float-right"
							>
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
	data() {
		return {
			isLoading: false,
		};
	},

	async asyncData({ params, $axios }) {
		const company = await $axios
			.get(`companies/${params.id}`)
			.then((resp) => {
				return resp.data.data;
			});

		return {
			form: new Form({
				name: company.name,
				full_address: company.full_address,
				zip_code: company.zip_code,
				phone_number: company.phone_number,
				official_email_address: company.official_email_address,
			}),
			company
		};
	},

	methods: {
		async edit() {
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {
				await this.$axios.put(
					`companies/${this.$route.params.id}`,
					this.form
				);

				this.$nuxt.$loading.finish();
				this.isLoading = false;

				this.$router.push(`/companies`);
			} catch (error) {
				console.log("ERROR!!", error);

				if (error.response && error.response.status == 422) {
					this.$nextTick(() => {
						this.form.errors.set(error.response.data.errors);
					});

					this.$toast.error(`${error.response.data.message}`, {
						icon: "exclamation-triangle",
						iconPack: "fontawesome",
						duration: 5000,
					});
				} else if (error.response && error.response.status == 403) {
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
				
				this.isLoading = false
				this.$nuxt.$loading.finish();
			}
		},
	},
};
</script>

<style>
</style>