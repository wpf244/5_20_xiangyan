! function (a) {
    var c, i, u, m, f;
    a.fn.extend({
        countDown: function (e) {
            var l = a.extend({}, t, e);
            this.each(function () {
                var t = a(this),
                    r = (new Date).getTime(),
                    o = new Date(l.startTimeStr).getTime(),
                    n = new Date(l.endTimeStr).getTime();
                o = o < (n = o < n ? n : o) ? o : n, c = setInterval(function () {
                    if ((r = (new Date).getTime()) <= o) t.beforeAction(l), clearInterval(c);
                    else if (r <= n) {
                        var e = n - r;
                        i = Math.floor(e / 1e3 / 60 / 60 / 24), u = Math.floor(e / 1e3 / 60 / 60 % 24), m = Math.floor(e / 1e3 / 60 % 60), f = Math.floor(e / 1e3 % 60), a(l.daySelector).html(t.doubleNum(i)), a(l.hourSelector).html(t.doubleNum(u)), a(l.minSelector).html(t.doubleNum(m)), a(l.secSelector).html(t.doubleNum(f))
                    } else t.afterAction(l), clearInterval(c)
                }, 1e3)
            })
        },
        doubleNum: function (e) {
            return e < 10 ? "0" + e : e + ""
        },
        beforeAction: function (e) {
            a(e.daySelector).parent().html("敬请期待")
        },
        afterAction: function (e) {
            a(e.daySelector).parent().html("活动结束")
        }
    });
    var t = {
        startTimeStr: "2017/01/10 00:00:00",
        endTimeStr: "2017/01/17 23:59:59",
        daySelector: ".day",
        hourSelector: ".hour",
        minSelector: ".min",
        secSelector: ".sec"
    }
}(jQuery);