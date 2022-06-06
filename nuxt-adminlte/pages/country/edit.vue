<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Edit Country - #{{ form.country_name || "-" }}</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">Country</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="edit" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Country</label>
								<div class="col-sm-8">
									<input
										v-model="form.country_name"
										type="text"
										class="form-control"
										id="country_name"
										placeholder="Country name"
										:class="{
											'is-invalid': form.errors.has('country_name'),
										}"
									/>
									<has-error
										:form="form"
										field="country_name"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Code</label>
								<div class="col-sm-8">
									<input
										v-model="form.code"
										type="text"
										class="form-control"
										placeholder="Country code"
										:class="{
											'is-invalid': form.errors.has('code'),
										}"
									/>
									<has-error :form="form" field="code"></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Status</label>
								<div class="col-sm-8">
									<input
										type="checkbox"
										v-model="form.status"
										class="form-check-input"
										id="status"
									/>
									<label class="form-check-label" for="status"
										>Active</label
									>
									<has-error :form="form" field="status"></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<my-button :loading="isLoading"> Update </my-button>
							<nuxt-link
								tag="a"
								to="/countries"
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
		const country = await $axios
			.get(`countries/${params.id}`)
			.then((resp) => {
				return resp.data.data;
			});

		return {
			form: new Form({
				country_name: country.country_name,
				code: country.code,
				status: country.status,
			}),
		};
	},

	methods: {
		async edit() {
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {
				await this.$axios.put(
					`countries/${this.$route.params.id}`,
					this.form
				);

				this.$nuxt.$loading.finish();
				this.isLoading = false;

				this.$router.push(`/countries`);
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

				this.isLoading = false;

				this.$nuxt.$loading.finish();
			}
		},
	},
};
</script>

<style>
</style>