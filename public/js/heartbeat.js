

var heartbeat = function (url, callback)
{
    this.url = url;
    this.callback = callback;
    this.lastupdate = 0;
    this.bussy = false;

    this.trigger = function()
    {
        var self = this;
        console.log('trigger called ' + this.bussy);
        if (self.bussy) return;
        self.bussy = true;
        $.get(self.url, function(data)
        {
            // Ignore first time.
            if (self.lastupdate == 0)
            {
                self.lastupdate = data;
                self.bussy = false;
                return;
            }

            if (data > self.lastupdate)
            {
                self.lastupdate = data;
                self.callback();
            }
            self.bussy = false;
        });
    }

}