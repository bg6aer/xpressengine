/**
 * <ToggleMenu type="comment@instnaceId" identifier={} class="btn btn-default btn-xs" icon="glyphicon glyphicon-cog" text="관리" itemClass="" data={} />
 */
var ToggleMenu = React.createClass({

    getInitialState: function () {
        return {
            items: [],
            loaded: false,
            firstLoaded: false
        };
    },

    reload: function () {
        this.setState({loaded: false});
    },

    getItems: function () {

        if (this.state.items.length < 1 && this.state.firstLoaded === false) {
            return React.createElement("li", {className: "text-center"}, React.createElement("span", null, "Loading..."));
        } else if (this.state.items.length < 1) {
                return React.createElement("li", {className: "text-center"}, React.createElement("span", null, "항목이 없습니다."));
        } else {
            return this.state.items.map(function (item, i) {
                var props = $.extend({}, item, {
                    identifier: this.props.identifier,
                    class: this.props.itemClass,
                    data: this.props.data,
                    reload: this.reload
                });
                return React.createElement(ToggleMenu.Item, React.__spread({},  props, {key: i}));
            }.bind(this));
        }
    },

    getIcon: function () {
        return this.props.icon ? React.DOM.span({className: this.props.icon}) : "";
    },

    getBody: function () {
        var classes = 'dropdown-toggle';
        if (this.props.class) {
            var temp = [classes];
            temp.push(this.props.class);
            classes = temp.join(' ');
        }

        var props = {className: classes, "data-toggle": "dropdown", "aria-expanded": "false", onClick: this.onClick};

        if (this.props.html) {
            $.extend(props, {dangerouslySetInnerHTML: {__html: this.props.html}});

            return React.DOM.span(props);
        }

        return React.DOM.i(props,
            this.getIcon(),
            " ",
            this.props.text
        );
    },

    onClick: function (e) {
        if (this.state.loaded === true) {
            return;
        }

        this.setState({loaded: true});

        $.ajax({
            url: '/plugin/toggleMenu',
            type: 'get',
            dataType: 'json',
            data: {type: this.props.type, id: this.props.identifier},
            success: function (json) {
                this.setState({items: json, firstLoaded: true});
            }.bind(this)
        });
    },

    render: function () {
        return (
            React.DOM.span({className: "dropdown v2"},
                this.getBody(),
                React.DOM.ul({className: "dropdown-menu", role: "menu"}, this.getItems())
            )
        );
    }
});


ToggleMenu.Item = React.createClass({

    componentWillMount: function () {
        if (this.props.script && $('script[src="' + this.props.script + '"]').is('script') !== true) {
            $.getScript(this.props.script);
        }
    },

    itemClick: function (e) {
        this.props.reload();
    },

    render: function () {
        if (this.props.type == "raw") {
            return React.DOM.li({onClick: this.itemClick, dangerouslySetInnerHTML: {__html: this.props.action}}, null);
        }

        var attr;
        switch (this.props.type) {
            case 'func' :
                attr = {href: '#', onClick: function (e) {
                    (eval(this.props.action))(this.props.data);
                    e.preventDefault();
                }.bind(this)};
                break;
            case 'exec' :
                attr = {href: '#', onClick: function (e) {
                    eval(this.props.action);
                    e.preventDefault();
                }.bind(this)};
                break;
            case 'link' :
                attr = {href: this.props.action};
                break;
        }

        return (
            React.DOM.li({className: this.props.class, onClick: this.itemClick},
                React.DOM.a(attr, this.props.text)
            )
        );
    }
});
