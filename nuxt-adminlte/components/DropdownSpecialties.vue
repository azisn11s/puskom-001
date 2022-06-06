<template>
	<v-select :options="specialties" v-model="selected" placeholder="Select a specialty" />
</template>

<script>
import { mapGetters } from "vuex";

export default {
	data() {
		return {
			specialties: [],
            selected: null
		};
	},

	computed: {
		...mapGetters({
			getterSpecialties: "specialties/getSpecialties",
		}),
	},

	mounted() {
		this.fetchSpecialties();
	},

	methods: {
		/**
		 * Fetch doctor's specialties
		 */
		fetchSpecialties() {
            if (this.getterSpecialties.length) {
                this.specialties = this.getterSpecialties
            }

            if (!this.getterSpecialties.length) {
                this.$axios.get(`search-specialty`).then((resp) => {
                    if (resp.data.items && resp.data.items.length) {
                        this.specialties = resp.data.items;
                        this.$store.dispatch(
                            "specialties/storeSpecialties",
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