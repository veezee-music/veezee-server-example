<style type="text/css" rel="stylesheet">

</style>

<script type="text/x-template" id="job-card">
    <div class="c-card job-card" :class="extras.col" style="display: inline;">
        <article class="row" :class="extras.borders">
            <div class="job-info" :class="{'col-lg-6': item.premium != undefined && item.perk == undefined && item.premium.active && item.premium.description != undefined && item.premium.description != null && item.premium.description != '', 'col-lg-4': item.premium != undefined && item.perk != undefined && item.premium.active && item.premium.description != undefined && item.premium.description != null && item.premium.description != '', 'col-lg-12': item.premium == undefined || !item.premium.active }">
                <header>
                    <div>
                        <h5 class="title">(( item.title ))</h5>
                        <small class="position-note">
                            <span v-if="item.type == 'internship'">(کارآموز)</span>
                        </small>
                    </div>
                    <div class="mini-spacer"></div>
                    <div>
                        <div class="employer">
                            <div v-if="item.premium != undefined && item.premium.active && item.employer.company.logo != undefined && item.employer.company.logo != null && item.employer.company.logo != ''">
                                <img width="50" src="/assets/img/alopek.jpg">
                            </div>
                            <div>
                                <h6>(( item.employer.company.name ))</h6>
                                <div class="location" :class="{ 'with-employer-logo': item.premium != undefined && item.premium.active }">
                                    <i class="fa fa-map-marker"></i>
                                    <em>(( item.location.city ))، (( item.location.state ))</em>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="half-spacer"></div>
                <section class="contract">
                    <div v-if="item.gender != undefined && (item.gender == 'خانم' || item.gender == 'آقا')">
                        <i class="fa-fw fa" :class="{ 'fa-female': item.gender == 'خانم', 'fa-male': item.gender == 'آقا' }"></i>
                        <span>(( item.gender ))</span>
                    </div>
                    <div v-if="item.type != 'internship'">
                        <i class="fa-fw fa fa-paste"></i>
                        <span>(( item.contract ))</span>
                    </div>
                    <div v-if="(item.salary.min != undefined && item.salary.min != null && item.salary.min != '') || (item.salary.max != undefined && item.salary.max != null && item.salary.max != '')">
                        <i class="fa-fw fa fa-money"></i>
                        <span v-if="item.salary.min != undefined && item.salary.min != null && item.salary.min != ''">حقوق از</span>
                        <span v-if="item.salary.min != undefined && item.salary.min != null && item.salary.min != ''"><span class="money">(( convertNumbersToPersian( toRial(item.salary.min) ) ))</span></span>
                        <span v-if="item.salary.max != undefined && item.salary.max != null && item.salary.max != ''">تا <span class="money">(( convertNumbersToPersian( toRial(item.salary.max) ) ))</span> تومان</span>
                    </div>
                </section>
                <div class="half-spacer"></div>
                <section class="skills">
                    <div class="label" v-for="skill in item.skills">(( skill ))</div>
                </section>
            </div>
            <div class="col-lg-6 extra" v-if="item.premium != undefined && item.premium.active && item.premium.description != undefined && item.premium.description != null && item.premium.description != ''">
                <p class="text">(( item.premium.description ))</p>
            </div>
            {{--v-if="item.perk != undefined" :class="{ 'col-lg-2': item.premium != undefined && item.premium.active, 'hidden': item.premium == undefined || !item.premium.active }"--}}
            <div class="col-lg-12" v-if="item.premium != undefined">
                <div class="perks" style="margin-top: .5rem;    display: flex;
    flex-direction: row;
    height: 100%;
    justify-content: center;
    align-items: center;">
                    <div style="width: 100%;     min-width: 6.5rem;
    text-align: center; background-color: rgb(237, 237, 237);
    border-radius: 0.4rem;
    padding: 0 .2rem; margin: 0 .1rem;">
                        <i style="display: inline-block; vertical-align: middle;" class="fa-fw fa fa-clock-o"></i>
                        <span style="font-family: Shabnam, Tahoma, sans-serif !important; font-size: .8rem;">ساعات منعطف</span>
                    </div>
                    <div style="margin: .2rem 0"></div>
                    <div style="width: 100%;     min-width: 6.5rem;
    text-align: center; background-color: rgb(237, 237, 237);
    border-radius: 0.4rem;
    padding: 0 .2rem; margin: 0 .1rem;">
                        <i style="display: inline-block; vertical-align: middle;" class="fa-fw fa fa-graduation-cap"></i>
                        <span style="font-family: Shabnam, Tahoma, sans-serif !important; font-size: .8rem;">آموزش</span>
                    </div>
                    <div style="margin: .2rem 0"></div>
                    <div style="width: 100%;    min-width: 6.5rem;
    text-align: center; background-color: rgb(237, 237, 237);
    border-radius: 0.4rem;
    padding: 0 .2rem; margin: 0 .1rem;">
                        <i style="display: inline-block; vertical-align: middle;" class="fa-fw fa fa-cutlery"></i>
                        <span style="font-family: Shabnam, Tahoma, sans-serif !important; font-size: .8rem;">ناهار رایگان</span>
                    </div>
                    <div style="margin: .2rem 0;"></div>
                    <div style="width: 100%;    min-width: 6.5rem;
    text-align: center; background-color: rgb(237, 237, 237);
    border-radius: 0.4rem;
    padding: 0 .2rem; margin: 0 .1rem;">
                        <i style="display: inline-block; vertical-align: middle;" class="fa-fw fa fa-medkit"></i>
                        <span style="font-family: Shabnam, Tahoma, sans-serif !important; font-size: .8rem;">بیمه</span>
                    </div>
                    <div style="margin: .2rem 0;"></div>
                    {{--<div style="width: 100%;--}}
    {{--text-align: center; background-color: rgb(237, 237, 237);--}}
    {{--border-radius: 0.4rem;--}}
    {{--padding: 0 .2rem; margin: 0 .1rem;">--}}
                        {{--<i style="display: inline-block; vertical-align: middle;" class="fa-fw fa fa-subway"></i>--}}
                        {{--<span style="font-family: Shabnam, Tahoma, sans-serif !important; font-size: .8rem;">دسترسی به مترو</span>--}}
                    {{--</div>--}}
                </div>
            </div>
        </article>
    </div>
</script>

<script>
    var jobCard = {
        template: '#job-card',
        delimiters: ["((","))"],
        props: ['item'],
        name: 'job-card',
        directives: {
        },
        data: function () {
            return {
                extras: {
                    col: '',
                    borders: ''
                }
            }
        },
        created: function () {
            this.fillTheCard();
        },
        mounted: function () {

        },
        methods: {
            fillTheCard: function () {
                this.extras.col = '';
                this.extras.borders = '';

                if(this.item.premium !== undefined && this.item.premium.active) {
                    this.extras.col += ' premium-card ';

                    var premium = this.item.premium;

                    if(premium !== undefined && premium.active) {
                        if(premium.borders !== undefined) {
                            if(premium.borders.color !== undefined && premium.borders.colorChange) {
                                this.extras.borders += 'card-attention card-attention-' + premium.borders.color;
                                if(premium.borders.flashing !== undefined && premium.borders.flashing) {
                                    this.extras.borders += ' card-attention-' + premium.borders.color + '-flashing';
                                }
                            }
                        }
                    }
                }

                if(this.item.premium !== undefined && this.item.premium.active) {
                    this.extras.col += 'col-lg-6 active-card';
                } else {
                    this.extras.col += 'col-lg-3';
                }
            }
        },
        computed: {
        },
        mixins: [globalMixin],
        watch: {
            'item': {
                handler: function (newVal) {
                    this.fillTheCard();
                },
                deep: true
            }
        }
    };
</script>