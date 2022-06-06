<template>
	<v-select
		multiple
		@open="onOpenTerritories"
		@search="fetchTerritoryOptions"
		:options="territories"
		v-model="selected"
		placeholder="Select territories"
	>
	</v-select>
</template>

<script>

export default {
	data() {
		return {
			territories: [],
            selected: null
		};
	},

	methods: {
		fetchTerritoryOptions(search, loading) {
			this.$axios
				.get(`search-territories`, {
					params: {
						term: search,
					},
				})
				.then((resp) => {
					if (resp.data.items) {
						this.territories = resp.data.items;
					}
				});
		},

		onOpenTerritories() {
			this.$axios.get(`search-territories`).then((resp) => {
				if (resp.data.items) {
					this.territories = resp.data.items;
				}
			});
		},
	},

    watch: {
        selected: function (newVal, oldVal) {
            this.$emit('onSelectedItem', newVal);
        }
    }
};
</script>

<style>
</style>