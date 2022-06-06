<template>
	<v-select :options="classifications" v-model="selected" placeholder="Select a classification" />
</template>

<script>
import { mapGetters } from "vuex";

export default {
	data() {
		return {
			classifications: [],
            selected: null
		};
	},

	computed: {
		...mapGetters({
			getterClassifications: "classifications/getClassifications",
		}),
	},

	mounted() {
		this.fetchClassifications();
	},

	methods: {
		/**
		 * Fetch doctor's classifications
		 */
		fetchClassifications() {
            if (this.getterClassifications.length) {
                this.classifications = this.getterClassifications
            }

            if (!this.getterClassifications.length) {
                this.$axios.get(`search-classification`).then((resp) => {
                    if (resp.data.items && resp.data.items.length) {
                        this.classifications = resp.data.items;
                        this.$store.dispatch(
                            "classifications/storeClassifications",
                            resp.data.items
                        );
                    }
                });
            }
			
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