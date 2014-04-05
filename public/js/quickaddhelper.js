var quickAddHelper = function(el)
{
    this.self = el;
    this.ship = [];

    this.init = function ()
    {
        var self = this;
        $( "#shipChooser" ).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: "index.php/ships/autocomplete",
                    dataType: "json",
                    data: {
                        featureClass: "P",
                        style: "full",
                        maxRows: 12,
                        name_startsWith: request.term
                    },
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                label: item.name + ', ' + item.category + ' (' + item.points + ')',
                                value: item.name,
                                category: item.category,
                                points: item.points
                            }
                        }));
                    }
                });
            },
            minLength: 2,
            select: function( event, ui ) {

                var idx = self.ship.length;
                ui.item.idx = idx;
                self.ship[idx]=  ui.item;

                $('#addedShips').append('<li id="ship_'+idx+'_li"><input name="ship_'+idx+'" type="hidden" value="'+ui.item.value+'" /><span class="shipName">'+ui.item.label+'</span> Qty:<input class="shipQty" name="ship_'+idx+'_qty" size="1" type="text" value="1" />&nbsp;<a class="removeShip" idx="'+idx+'"><img width="16" src="/img/icon/Black_Remove.png" /></a></li>')

                $('.shipQty').off('change');
                $('.shipQty').on('change', function() { self.updateStats() });

                $('.removeShip').off('click');
                $('.removeShip').on('click', function() { self.removeShip(self, $(this).attr('idx')) });

                self.updateStats();
            },
            open: function() {
                $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
        });
    };

    this.removeShip = function(self, idx)
    {
        self.ship[idx] = false;
        $('#ship_'+idx+'_li').remove();
        self.updateStats();
    }

    this.updateStats = function()
    {
        var self = this;
        var points=0;
        var pilots=0;

        $(self.ship).each(function()
        {
            if (this != false)
            {
                var qty = parseInt($('input[name=ship_'+ this.idx +'_qty]').val());
                points = points + (this.points * qty);
                pilots = pilots + qty;
            }
        })

        $('#pointCount').text(points);
        $('#pilotCount').text(pilots);
    };
}