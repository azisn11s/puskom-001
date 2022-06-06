<template>
  <div class="row">
			<div class="col-lg-12">
				<div class="text-center section">
					<h2 class="h2">Custom Calendars</h2>
					<p class="text-lg font-medium text-gray-600 mb-6">
						Roll your own calendars using scoped slots
					</p>
					<vc-calendar
						class="custom-calendar max-w-full"
						:masks="masks"
						:attributes="attributes"
						disable-page-swipe
						is-expanded
					>
						<template v-slot:day-content="{ day, attributes }">
							<div class="z-10 overflow-hidden c-day-container">
								<a href="#" rel="noopener noreferrer">
									<span class="day-label text-lg text-black"> {{ day.day }} </span>
								</a>
								<div class="flex-grow overflow-y-auto overflow-x-auto">
									<p
										v-for="attr in attributes"
										:key="attr.key"
										class="
											text-xs
											leading-tight
											rounded-sm
											p-1
											mt-0
											mb-1
										"
										:class="attr.customData.class"
									>
										{{ attr.customData.title }}
									</p>
								</div>
							</div>
						</template>
					</vc-calendar>
				</div>
			</div>

		</div>
</template>

<script>
export default {
    data() {
		const month = new Date().getMonth();
		const year = new Date().getFullYear();

		return {
			masks: {
				weekdays: "WWW",
			},

			attributes: [
				{
					key: 1,
					customData: {
						title: "Lunch with mom.",
						class: "bg-red text-white",
					},
					dates: new Date(year, month, 1),
				},
				{
					key: 2,
					customData: {
						title: "Take Noah to basketball practice",
						class: "bg-blue text-white",
					},
					dates: new Date(year, month, 2),
				},
				{
					key: 3,
					customData: {
						title: "Noah's basketball game.",
						class: "bg-blue text-white",
					},
					dates: new Date(year, month, 5),
				},
				{
					key: 4,
					customData: {
						title: "Take car to the shop",
						class: "bg-indigo text-white",
					},
					dates: new Date(year, month, 5),
				},
				{
					key: 4,
					customData: {
						title: "Meeting with new client.",
						class: "bg-teal text-white",
					},
					dates: new Date(year, month, 7),
				},
				{
					key: 5,
					customData: {
						title: "Mia's gymnastics practice.",
						class: "bg-pink text-white",
					},
					dates: new Date(year, month, 11),
				},
				{
					key: 6,
					customData: {
						title: "Cookout with friends.",
						class: "bg-orange text-white",
					},
					dates: { months: 5, ordinalWeekdays: { 2: 1 } },
				},
				{
					key: 7,
					customData: {
						title: "Mia's gymnastics recital.",
						class: "bg-pink text-white",
					},
					dates: new Date(year, month, 22),
				},
				{
					key: 8,
					customData: {
						title: "Visit great grandma.",
						class: "bg-red text-white",
					},
					dates: new Date(year, month, 25)
				},
			],
		};
	},
}
</script>

<style lang="postcss" scoped>
.custom-calendar {
  border-radius: 0;
}

/deep/ .vc-header {
    background-color: #b9def8;
    padding: 10px 0;
  }

/deep/ .vc-weeks {
    padding: 0;
  }

/deep/ .vc-weekday {
    background-color: #dce9f7;
    border-bottom: 1px solid #eaeaea;
    border-top: 1px solid #eaeaea;
	color: rgb(0, 0, 0);
    padding: 5px 0;
  }

/deep/ .vc-day-dots {
    margin-bottom: 5px;
  }

/deep/ .vc-day {
	border: 1px solid #b8c2cc;
    padding: 0 5px 3px 5px;
    text-align: left;
    height: 90px;
    min-width: 90px;
    background-color: white;    
    &:not(.on-bottom) {
      border-bottom: 1px solid #b8c2cc;
      &.weekday-1 {
        border-bottom: 1px solid #b8c2cc;
      }
    }
    &:not(.on-right) {
      border-right: 1px solid #b8c2cc;
    }
  }

  /deep/ .vc-day:hover {
	  position: relative;
	  height: 100%;
	  background-color: rgba(0, 255, 255, 0.171);
  }

/deep/ .weekday-1 {
      background-color: #eff8ff;
    }

/deep/ .is-today{
	background-color: #b9f36c;
}

::-webkit-scrollbar {
  width: 0px;
}
::-webkit-scrollbar-track {
  display: none;
}

</style>