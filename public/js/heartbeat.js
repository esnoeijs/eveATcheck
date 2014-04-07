

var heartbeat = function (url, callback)
{
    this.url = url;
    this.callback = callback;
    this.lastupdate = 0;


    this.trigger = function()
    {
        var self = this;
        $.get(self.url, function(data){
            if (data > self.lastupdate)
            {
                self.lastupdate = data;
                self.callback();
            }
        });
    }

}