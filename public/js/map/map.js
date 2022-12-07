treeJson = d3.json("https://raw.githubusercontent.com/ErikGartner/dTree/master/test/demo/data.json", function (error, treeData) {

    dTree.init(treeData, {
        target: "#graph",
        debug: false,
        hideMarriageNodes: false,
        marriageNodeSize: 5,
        height: 800,
        width: 1200,
        margin: {
            top: 10,
            right: 10,
            bottom: 10,
            left: 10
        },
        callbacks: {
            nodeClick: function (name, extra) {
                alert('Click: ' + name);
            },
            nodeRightClick: function (name, extra) {
                alert('Right-click: ' + name);
            },
            textRenderer: function (name, extra, textClass) {
                if (extra && extra.nickname)
                    name = name + " (" + extra.nickname + ")";



                return "<p align='center' class='" + textClass + "'>" + name + "</p>";
            },
            marriageClick: function (extra, id) {
                alert('Clicked marriage node' + id);
            },
            marriageRightClick: function (extra, id) {
                alert('Right-clicked marriage node' + id);
            }
        }
    });
});