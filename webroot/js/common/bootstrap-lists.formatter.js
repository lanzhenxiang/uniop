/**
 * 格式化时间戳
 * @param  string value 时间戳
 * @return string      日期格式字符串eg:2017-02-16 05:35:33
 */
function timestrap2date(value){
    var createDate = new Date(parseInt(value) * 1000);
    return createDate.pattern("yyyy-MM-dd HH:mm:ss");
}
