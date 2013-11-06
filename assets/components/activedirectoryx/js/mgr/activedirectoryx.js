var ActiveDirectoryX = function(config) {
    config = config || {};
    ActiveDirectoryX.superclass.constructor.call(this, config);
};

Ext.extend(ActiveDirectoryX,Ext.Component,{
    page:{}, window:{}, grid:{}, tree:{}, panel:{}, combo:{}, config: {}, view: {}
});

Ext.reg('activedirectoryx', ActiveDirectoryX);

var ActiveDirectoryX = new ActiveDirectoryX();