ActiveDirectoryX.panel.Home = function(config) {
    config = config || {};

    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,items: [{
            html: '<h2>' + _('activedirectoryx') + '</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,bodyStyle: 'padding: 10px'
            ,defaults: {
                border: false
                ,autoHeight: true
            }
            ,border: true
            ,activeItem: 0
            ,hideMode: 'offsets'
            ,items: [{
                title: _('activedirectoryx.home')
                ,items: [{
                    html: '<p>' + _('activedirectoryx.intro_msg') + '</p><br />'
                    ,border: false
                }]
            }]
        }]
    });

    ActiveDirectoryX.panel.Home.superclass.constructor.call(this, config);
};

Ext.extend(ActiveDirectoryX.panel.Home, MODx.Panel);
Ext.reg('activedirectoryx-panel-home', ActiveDirectoryX.panel.Home);
