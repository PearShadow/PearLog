leantime.dateController = (function () {

    function getBaseDatePickerConfig(callback) {

        return {
            numberOfMonths: 1,
            dateFormat: leantime.dateHelper.getFormatFromSettings("dateformat", "jquery"),
            dayNames: leantime.i18n.__("language.dayNames").split(","),
            dayNamesMin: leantime.i18n.__("language.dayNamesMin").split(","),
            dayNamesShort: leantime.i18n.__("language.dayNamesShort").split(","),
            monthNames: leantime.i18n.__("language.monthNames").split(","),
            monthNamesShort: leantime.i18n.__("language.monthNamesShort").split(","),
            currentText: leantime.i18n.__("language.currentText"),
            closeText: leantime.i18n.__("language.closeText"),
            buttonText: leantime.i18n.__("language.buttonText"),
            isRTL: leantime.i18n.__("language.isRTL") === "true" ? 1 : 0,
            nextText: leantime.i18n.__("language.nextText"),
            prevText: leantime.i18n.__("language.prevText"),
            weekHeader: leantime.i18n.__("language.weekHeader"),
            firstDay: leantime.i18n.__("language.firstDayOfWeek"),
            onSelect: callback

        };
    }

    function getDate(element) {

        var dateFormat = leantime.dateHelper.getFormatFromSettings("dateformat", "jquery");
        var date;

        try {
            date = jQuery.datepicker.parseDate(dateFormat, element.value);
        } catch (error) {
            date = null;
            console.log(error);
        }

        return date;
    }

    var initDateRangePicker = function (fromElement, toElement, minDistance) {

        Date.prototype.addDays = function (days) {
            this.setDate(this.getDate() + days);
            return this;
        };

        //Check for readonly status and disable datepicker if readonly
        jQuery.datepicker.setDefaults({
            beforeShow: function (i) {
                if (jQuery(i).attr('readonly')) {
                    return false;
                }
            }
        });

        var from = jQuery(fromElement).datepicker(getBaseDatePickerConfig())
            .on(
                "change",
                function (date) {
                    to.datepicker("option", "minDate", getDate(this));

                    if (jQuery(toElement).val() == '') {
                        jQuery(toElement).val(jQuery(fromElement).val());
                    }
                }
            );

        var to = jQuery(toElement).datepicker(getBaseDatePickerConfig())
            .on(
                "change",
                function () {
                    from.datepicker("option", "maxDate", getDate(this));
                }
            );
    };

    var initDatePicker = function (element, callback) {
        jQuery(element).datepicker(
            getBaseDatePickerConfig(callback)
        );
    }
    var initModernDateRangePicker = function (fromElement, toElement, minDistance) {

        var jqueryFormat = leantime.dateHelper.getFormatFromSettings("dateformat", "jquery");
        var userFormat = convertJqueryToMomentFormat(jqueryFormat);

        var fromValue = jQuery(fromElement).val();
        var toValue = jQuery(toElement).val();

        var startDate = fromValue
            ? moment(fromValue, [userFormat, 'YYYY-MM-DD', 'MM/DD/YYYY', 'DD.MM.YYYY'], true)
            : moment().startOf('month');
        var endDate = toValue
            ? moment(toValue, [userFormat, 'YYYY-MM-DD', 'MM/DD/YYYY', 'DD.MM.YYYY'], true)
            : moment().endOf('month');

        if (!startDate.isValid()) startDate = moment().startOf('month');
        if (!endDate.isValid()) endDate = moment().endOf('month');

        var displayFormat = userFormat.replace(/ddd+,?\s*/g, '').trim();

        jQuery(fromElement).daterangepicker({
            autoUpdateInput: false,
            opens: 'left',
            linkedCalendars: true,
            startDate: startDate,
            endDate: endDate,
            minDate: moment().subtract(1, 'years'),
            maxDate: moment().add(1, 'years'),
            locale: {
                format: displayFormat,
                applyLabel: leantime.i18n.__("language.applyLabel") || 'Apply',
                cancelLabel: leantime.i18n.__("language.cancelLabel") || 'Cancel',
                fromLabel: leantime.i18n.__("language.fromLabel") || 'From',
                toLabel: leantime.i18n.__("language.toLabel") || 'To',
                customRangeLabel: 'Custom',
                firstDay: parseInt(leantime.i18n.__("language.firstDayOfWeek")) || 1
            },
            ranges: {
                'Today': [moment().startOf('day'), moment().endOf('day')],
                'This Week': [moment().startOf('week'), moment().endOf('week')],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()]
            }
        });

        if (!fromValue) jQuery(fromElement).val(startDate.format(displayFormat));
        if (!toValue) jQuery(toElement).val(endDate.format(displayFormat));

        jQuery(fromElement).on('apply.daterangepicker', function (ev, picker) {
            jQuery(fromElement).val(picker.startDate.format('YYYY-MM-DD'));
            jQuery(toElement).val(picker.endDate.format('YYYY-MM-DD'));
            jQuery('#form').submit();
        });

        jQuery(toElement).on('focus click', function (e) {
            e.preventDefault();
            jQuery(fromElement).data('daterangepicker').show();
        });
    };

    function convertJqueryToMomentFormat(jqueryFormat) {
        if (!jqueryFormat) return 'DD.MM.YYYY';

        return jqueryFormat
            .replace(/DD/g, 'dddd')
            .replace(/D/g, 'ddd')
            .replace(/MM/g, 'MMMM')
            .replace(/M/g, 'MMM')
            .replace(/mm/g, 'MM')
            .replace(/m/g, 'M')
            .replace(/dd/g, 'DD')
            .replace(/d/g, 'D')
            .replace(/yy/g, 'YYYY')
            .replace(/y/g, 'YY');
    }


    return {
        initDateRangePicker: initDateRangePicker,
        initModernDateRangePicker: initModernDateRangePicker,
        initDatePicker: initDatePicker,
    };

})();
