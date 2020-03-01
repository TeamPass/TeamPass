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

Ext.define('TeamPass.view.GroupTreePanel', {
    extend: 'Ext.tree.Panel',
    alias: 'widget.grouptreepanel',
    initComponent: function() {
        lastFilterValue = "";
        Ext.apply(this, {
            store: 'GroupTree',
            lines: false,
            useArrows: false,
            rootVisible: false,
            border:true,
            animate:false,
            hideHeaders:true,
            columns: [{
                xtype: 'treecolumn',
                width: '100%',
                sortable: false,
                dataIndex: 'text',
                locked: false
            }],
            dockedItems: [{
                xtype: 'textfield',
                dock: 'top',
                emptyText: 'Search',
                enableKeyEvents: true,

                triggers: {
                    clear: {
                        cls: 'x-form-clear-trigger',
                        handler: 'onClearTriggerClick',
                        hidden: true,
                        scope: 'this'
                    }
                },

                onClearTriggerClick: function() {
                    this.setValue();
                    this.up().getStore().clearFilter();
                    this.getTrigger('clear').hide();
                },
                listeners: {
                    keyup: {
                        fn: function(field, event, eOpts) {
                            var value = field.getValue();

                            // Only filter if they actually changed the field value.
                            // Otherwise the view refreshes and scrolls to top.
                            if (value == '') {
                                field.getTrigger('clear').hide();

                                this.filterStore(value);
                                this.lastFilterValue = value;
                            } else if (value && value !== this.lastFilterValue) {
                                field.getTrigger('clear')[(value.length > 0) ? 'show' : 'hide']();
                                this.filterStore(value);
                                this.lastFilterValue = value;
                            }
                        },
                        buffer: 300
                    },
                    scope: this
                }
            }],
            filterStore: function (value) {
                var me = this,
                    store = me.store,
                    searchString = value.toLowerCase(),
                    filterFn = function (node) {
                        var children = node.childNodes,
                            len = children && children.length,
                            visible = v.test(node.get('text')),
                            i;

                        // If the current node does NOT match the search condition
                        // specified by the user...
                        if (!visible) {

                            // Check to see if any of the child nodes of this node
                            // match the search condition.  If they do then we will
                            // mark the current node as visible as well.
                            for (i = 0; i < len; i++) {
                                if (children[i].isLeaf()) {
                                    visible = children[i].get('visible');
                                } else {
                                    visible = filterFn(children[i]);
                                }
                                if (visible) {
                                    break;
                                }
                            }

                            if (!visible) {
                                var current = node;
                                while (current.parentNode) {
                                    current = current.parentNode;
                                    if (v.test(current.get('text'))) {
                                        visible = true;
                                        break;
                                    }
                                }
                            }
                        } else { // Current node matches the search condition...

                            // Force all of its child nodes to be visible as well so
                            // that the user is able to select an example to display.
                            for (i = 0; i < len; i++) {
                                children[i].set('visible', true);
                            }

                        }

                        return visible;
                    },
                    v;

                if (searchString.length < 1) {
                    store.clearFilter();
                } else {
                    v = new RegExp(searchString, 'i');
                    store.getFilters().replaceAll({
                        filterFn: filterFn
                    });
                }
            },
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    containerScroll: true
                }
            },
            listeners: {
                viewready: function (tree) {
                    var view = tree.getView(),
                        dd = view.findPlugin('treeviewdragdrop');

                    dd.dragZone.onBeforeDrag = function (data, e) {
                        var rec = view.getRecord(e.getTarget(view.itemSelector));
                        return rec.get("pDelete");
                    };
                    dd.dragZone.beforeDragOver = function (data, e) {
                        if (e.getTarget(view.itemSelector) !== null) {
                            var rec = view.getRecord(e.getTarget(view.itemSelector));
                            return rec.get("pCreate");
                        }
                        return false;
                    };
                }
            },
            setRootNode: function() {
                if (this.getStore().autoLoad) {
                    this.callParent(arguments);
                }
            }
        });
        this.callParent();
    },
});
