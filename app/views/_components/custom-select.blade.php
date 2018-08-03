<style type="text/css" rel="stylesheet">
    .form-input-container .tag {
        flex-wrap: wrap;
        display: flex;
        flex-direction: row;
        padding: .5rem 0;
    }

    .form-input-container .tag .label {
        font-family: Shabnam, Tahoma, sans-serif !important;
        font-size: .8rem;
        border: 1px solid transparent;
        border-radius: .4rem;
        background: #f2f3f4;
        color: #7e7e7e;
        padding: .1rem .5rem;
        margin: .1rem;
        align-items: baseline;
    }

    .tag .label span {
        font-weight: bolder;
        cursor: pointer;
    }
</style>

<script type="text/x-template" id="custom-select">
    <div>
        <div class=" custom-input-group">
            <button class="btn btn-secondary btn-sm input-button input-right" v-on:click="push(single)">ثبت</button>
            <v-select
                    id="custom-vselect-dropdown"
                    class="single-select"
                    placeholder="حداکثر ۵ مورد را انتخاب کنید"
                    :on-change="componentOnChange"
                    :on-search="componentOnSearch"
                    :options="options" v-on:keyup.native.enter="push(single)">
                <div slot="no-options">نتیجه ای یافت نشد.</div>
            </v-select>
        </div>
        <span class="message error"></span>
        <div class="form-input-container">
            <section class="tag" v-if="items.length > 0">
                <div class='label' v-for="(item , index) in items"><span v-on:click='splice(index)'>&times;</span> (( item ))</div>
            </section>
        </div>
    </div>
</script>

<script>
    var customSelect = {
        template: '#custom-select',
        name: 'app',
        delimiters: ['(( ', ' ))'],
        props: ['options'],
        data: function () {
          return {
              single: '',
              items: []
          }
        },
        components: {vSelect: VueSelect.VueSelect},
        methods: {
            componentOnChange: function (newVal) {
                this.single = newVal;
                this.addSpan(newVal);
            },
            componentOnSearch: function (newVal) {
                if (this.single.length <= newVal.length && this.options.indexOf(newVal) === -1) {
                    if (this.single.length > 0)
                        this.options.pop();
                    this.options.push(newVal);
                }
                this.single = newVal;
            },
            splice: function (index) {

                this.options.push(this.items[index]);
                this.items.splice(index, 1);
                this.$emit('get-items', this.items);
            },
            push: function (singleItem) {
                var self = this;
                this.items.push(singleItem);
                this.removeSpan();
                this.single = '';
                var index = this.options.indexOf(singleItem);
                $.each(this.options, function (k, v) {
                    if (k === index) {
                        self.options.splice(index, 1);
                    }
                });
                this.$emit('get-items', this.items);
            },
            removeSpan: function () {
                $('#custom-vselect-dropdown span').html('&nbsp;');
                $('#custom-vselect-dropdown span').css({display: 'none'});
                return true;
            },
            addSpan: function (value) {
                $('#custom-vselect-dropdown span').text(value);
                $('#custom-vselect-dropdown span').css({display: 'inline-flex'});
                return true;
            }
        }
    }
</script>