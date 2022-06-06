<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Edit Classification - #{{ classification.slug }}</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">Classification</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="edit" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"
									>Classification</label
								>
								<div class="col-sm-8">
									<input
										v-model="form.classification"
										type="text"
										class="form-control"
										id="classification"
										placeholder="Classification name"
										:class="{
											'is-invalid':
												form.errors.has('classification'),
										}"
									/>
									<has-error
										:form="form"
										field="classification"
									></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						<div class="card-footer">
							<my-button :loading="isLoading"> Update </my-button>
							<nuxt-link
								tag="a"
								to="/doctors/classifications"
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
		const classification = await $axios
			.get(`doctor-classifications/${params.id}`)
			.then((resp) => {
				return resp.data.data;
			});

		return {
			classification,
			form: new Form({
				classification: classification.classification,
			}),
		};
	},

	methods: {
		async edit() {
			this.$nuxt.$loading.start();
			this.isLoading = true;
			try {
				await this.$axios.put(
					`doctor-classifications/${this.$route.params.id}`,
					this.form
				);

				this.$nuxt.$loading.finish();
				this.isLoading = false;

				this.$router.push(`/doctors/classifications`);
			} catch (error) {
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

	computed: {
		formErrors() {
			return this.form.errors;
		},
	},
};
</script>

<style>
</style>