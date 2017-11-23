function urlmake(url,pram){
    if(url.indexOf("?")!=-1){
        return url+'&'+pram;
    }else{
        return url+'?'+pram;
    }
}

var color_arr = ['#7a96a4','#cba952','#667b16','#a26642','#349898','#c04f51','#5c315e','#445a2b','#adae50','#14638a','#b56367','#a399bb','#070dfa','#47ff07','#f809b7'];

Date.prototype.format = function (format) {
    format = format || 'YYYY-MM-DD hh:mm:ss';
    var date = {
        YYYY: this.getFullYear(),
        YY: this.getYear(),
        MM: (this.getMonth() > 8 ? '' : '0') + (this.getMonth() + 1),
        M: this.getMonth() + 1,
        DD: (this.getDate() > 9 ? '' : '0') + this.getDate(),
        D: this.getDate(),
        hh: this.getHours(),
        mm: (this.getMinutes() > 9 ? '' : '0') + this.getMinutes(),
        ss: (this.getSeconds() > 9 ? '' : '0') + this.getSeconds(),
        h: this.getHours(),
        m: this.getMinutes(),
        s: this.getSeconds()
    };
    var arr = format.match(/[a-zA-Z]+/g);
    for (var i = 0; i < arr.length; i++) {
        format = format.replace(arr[i], date[arr[i]] || '');
    }
    return format;
}