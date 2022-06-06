<template>
	<div class="content-fluid">
		<portal to="header-title">
			<span>Key Message & E-Detailing - {{ brand.brand_name }} #{{ brand.id }}</span>
		</portal>

		<div class="row">
			<div class="col-md-6">
				<div class="card card-info">
					<div class="card-header">
						<h3 class="card-title">Brand</h3>
					</div>
					<!-- /.card-header -->
					<!-- form start -->
					<form @submit.prevent="edit" class="form-horizontal">
						<div class="card-body">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Brand</label>
								<div class="col-sm-8">
									<input
										v-model="form.brand_name"
										type="text"
										class="form-control form-control-border border-width-2" disabled
										id="brand_name"
										placeholder="Brand name"
										:class="{
											'is-invalid': form.errors.has('brand_name'),
										}"
									/>
									<has-error
										:form="form"
										field="brand_name"
									></has-error>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Status</label>
								<div class="col-sm-8">
									<input
										type="checkbox"
										v-model="form.is_active"
										class="form-check-input" disabled
										id="is_active"
									/>
									<label class="form-check-label" for="is_active"
										>Active</label
									>
									<has-error :form="form" field="is_active"></has-error>
								</div>
							</div>
						</div>
						<!-- /.card-body -->
						
					</form>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-6">
				<brand-key-message :brand-id="brand.id" />
			</div>
			<div class="col-6">
				<brand-edetailing :brand-id="brand.id" />
			</div>
		</div>
	</div>
</template>

<script>
import Form from "vform";
import BrandEdetailing from '~/components/BrandEdetailing.vue';
import BrandKeyMessage from '~/components/BrandKeyMessage.vue';

export default {
	components: {
		BrandKeyMessage,
		BrandEdetailing
	},
	data() {
		return {
			isLoading: false,
		};
	},

	async asyncData({ params, $axios }) {
		const brand = await $axios
			.get(`brands/${params.id}`)
			.then((resp) => {
				return resp.data.data;
			});

		return {
			brand,
			form: new Form({
				brand_name: brand.brand_name,
				is_active: brand.is_active,
			}),
		};
	}
};
</script>

<style>
</style>