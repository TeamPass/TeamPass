/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to version 3 of the GPL license,
 * that is bundled with this package in the file LICENSE, and is
 * available online at http://www.gnu.org/licenses/gpl.txt
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

Ext.define('TeamPass.controller.Groups', {
    extend: 'TeamPass.controller.Abstract',

    refs: [
        {
            selector: 'grouptreepanel',
            ref: 'GroupTreePanel'
        }, {
            selector: 'menupanel',
            ref: 'menuPanel'
        }
    ],

    models: ['GroupTree'],

    stores: ['GroupTree'],

    init: function() {
        this.control({
            'grouptreepanel': {
                activate : this.onGroupTreePanelTabVisible,
                itemcontextmenu: this.onContextMenuClick,
                itemclick: this.onClick
            }
        });

        this.listen({
            store: {
                '#GroupTree': {
                    beforeload: this.loadGroupTreeStore,
                    beforesync: this.syncGroupTreeStore
                }
            }
        });

        this.application.on({
            appstart: this.onAppStart,
            scope: this
        });
    },

    /**
     * called before the store is loaded
     *
     * @param {Ext.data.Store} store a store
     *
     * @returns void
     */
    loadGroupTreeStore: function(store) {
        this.setCSRFToken(store);
    },

    /**
     * called before each store sync
     *
     * @returns void
     */
    syncGroupTreeStore: function() {
        this.setCSRFToken(this.getGroupTreeStore());
    },

    /**
     * event listener method
     *
     * @returns void
     */
    onAppStart: function() {
        this.getGroupTreeStore().load();
    },

    /**
     * reloads the tree store
     *
     * @returns void
     */
    onReloadTreeClick: function() {
        this.getGroupTreeStore().load();
    },

    /**
     * fires a event to reset the group tree panel
     *
     * @returns void
     */
    onGroupTreePanelTabVisible: function() {
        this.application.fireEvent('resetGroup');
    },

    /**
     * opens a leaf record in element grid
     *
     * @param dv
     * @param record
     *
     * @return void
     */
    onClick: function(dv, record) {
        // only fire event if record is leaf
        if(record.get('leaf') == 1) {
            rec = [];
            rec['id'] = record.get('id');
            rec['text'] = record.get('text');
            rec['leaf'] = record.get('leaf');

            this.application.fireEvent('openGroup', rec);
        } else {
            this.application.fireEvent('clearGroup');
        }
    },

    /**
     * creates a context menu on current mouse position
     *
     * @param view
     * @param rec
     * @param node
     * @param index
     * @param e
     *
     * @return void
     */
    onContextMenuClick : function(view, rec, node, index, e ) {
        var position = e.getXY();
        e.stopEvent();

        var menu = this.createNonLeafContextMenu(rec);
        menu.showAt(position);
    },

    /**
     * creates a menu for given leaf record
     *
     * @param {Ext.data.Model} rec the record
     *
     * @returns {Ext.menu.Menu}
     */
    createNonLeafContextMenu: function(rec) {
        var cmenu = Ext.create('Ext.menu.Menu', {
            scope:this,
            margin: '0 0 10 0'
        });

        if (rec.get('pCreate') === true) {
            cmenu.add({
                    text: 'Neue Gruppe',
                    iconCls: 'x-fa fa-folder-open-o',
                    scope:this,
                    handler : Ext.bind(this.onNewGroupClick, this, rec, true)
                },
                {
                    text: 'Neuer Eintrag',
                    iconCls: 'x-fa fa-file-text-o',
                    scope:this,
                    handler : Ext.bind(this.onNewEntryClick, this, rec, true)
                });
        }

        if (rec.get('pUpdate') === true && rec.get('isRoot') === false) {
            cmenu.add({
                text: 'Editieren...',
                iconCls: 'x-fa fa-pencil',
                scope:this,
                handler : Ext.bind(this.onEditClick, this, rec, true)
            });
        }

        if (rec.get('pDelete') === true && rec.get('isRoot') === false) {

            var disabled = false;
            if (rec.hasChildNodes()) {
                disabled = true;
            }

            cmenu.add({
                text: 'Löschen',
                iconCls: 'x-fa fa-trash-o',
                disabled: disabled,
                scope: this,
                handler : Ext.bind(this.onDeleteClick, this, rec, true)
            });
        }

        if (rec.get('isRoot') === true) {
            cmenu.add({
                text: 'Reload Tree',
                iconCls: 'x-fa fa-refresh',
                scope: this,
                handler:this.onReloadTreeClick
            });
        }

        return cmenu;
    },

    /**
     * creates a msg box to define a new group
     *
     * @param view
     * @param menuitem
     * @param {Ext.data.Model} rec
     *
     * returns void
     */
    onNewGroupClick: function(view, menuitem, rec) {
        var msgbox = Ext.Msg.prompt('create Group', 'Group-Name:', function(btn, text){
            if (btn == 'ok'){
                if (text) {

                    // appending is only allowed on non-leaf elements
                    // if element is leaf we use the parent node to append a new element
                    if(rec.get("leaf") === true) {
                        rec = rec.parentNode;
                    }

                    rec.appendChild({
                        "text": text,
                        "leaf": false
                    });

                }
            }
        },this, false);
        msgbox.textField.inputEl.dom.type = 'text';
    },

    /**
     * creates a msg box to define a new entry
     *
     * @param view
     * @param menuitem
     * @param {Ext.data.Model} rec
     *
     * @returns void
     */
    onNewEntryClick: function(view, menuitem, rec) {
        var msgbox = Ext.Msg.prompt('create Entry', 'Group-Name:', function(btn, text){
            if (btn == 'ok'){
                if (text) {

                    // appending is only allowed on non-leaf elements
                    // if element is leaf we use the parent node to append a new element
                    if(rec.get("leaf") === true) {
                        rec = rec.parentNode;
                    }

                    rec.appendChild({
                        "text": text,
                        "leaf": true,
                        "expanded":false
                    });
                }
            }
        },this, false);
        msgbox.textField.inputEl.dom.type = 'text';
    },

    /**
     * opens a message box to confirm deletion of selected record
     *
     * @param view
     * @param menuitem
     * @param rec
     *
     * @returns void
     */
    onDeleteClick: function(view, menuitem, rec) {
        if (rec) {
            var msgbox = Ext.Msg.confirm('are you sure?', 'do you really want to delete this Group?', function(btn){
                if (btn == 'yes'){
                    rec.remove();
                }
            },this);
        }
    },

    /**
     * opens a message box to change the name of selected record
     *
     * @param view
     * @param menuitem
     * @param rec
     *
     * @returns void
     */
    onEditClick: function(view, menuitem, rec) {
        rec = this.getGroupTreePanel().getSelectionModel().getSelection()[0];

        var msgbox = Ext.Msg.prompt('"'+ rec.get('text') +'" bearbeiten', 'Gruppen-Name:', function(btn, text){
            if (btn == 'ok'){
                rec.set('text',text);
            }
        },this, false, rec.get('text'));
        msgbox.textField.inputEl.dom.type = 'text';
    }
});
