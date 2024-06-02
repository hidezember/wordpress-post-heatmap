document.addEventListener('DOMContentLoaded', function() {
    var chartDom = document.getElementById('heatmap');
    var myChart = echarts.init(chartDom);
    window.onresize = function() {
        myChart.resize();
    };

    var dataMap = new Map(Object.entries(heatmapData.posts));

    var data = [];
    for (const [key, value] of dataMap.entries()) {
        data.push([key, value.length]);
    }

    var startDate = new Date();
    var year_Mill = startDate.setFullYear((startDate.getFullYear() - 1));
    startDate = +new Date(year_Mill);
    var endDate = +new Date();

    startDate = echarts.format.formatTime('yyyy-MM-dd', startDate);
    endDate = echarts.format.formatTime('yyyy-MM-dd', endDate);

    var prefersDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

    var lightTheme = {
        backgroundColor: '#FFFFFF',
        fangkuaicolor: '#F4F4F4',
        gaoliangcolor: ['#ffd0b6'],
        riqiColor: '#999',
        textbrcolor: '#FFF',
        xiankuangcolor: 'rgba(0, 0, 0, 0.0)',
    };

    var darkTheme = {
        backgroundColor: '#1A1718',
        fangkuaicolor: '#282325',
        gaoliangcolor: ['#b25f2f'],
        riqiColor: '#666',
        textbrcolor: '#332D2F',
        xiankuangcolor: 'rgba(0, 0, 0, 0.0)',
    };

    var currentTheme = prefersDarkMode ? darkTheme : lightTheme;

    var option = {
        tooltip: {
            hideDelay: 1000,
            enterable: true,
            backgroundColor: currentTheme.textbrcolor,
            borderWidth: 0,
            formatter: function (p) {
                const date = p.data[0];
                const posts = dataMap.get(date);
                var content = `<span style="font-size: 0.75rem;font-family: var(--font-family-code);">${date}</span>`;
                for (const post of posts) {
                    var link = post.url;
                    var title = post.title;
                    content += `<br><a href="${link}" target="_blank">${title}</a><br>`;
                }
                return content;
            }
        },
        visualMap: {
            show: false,
            inRange: { color: currentTheme.gaoliangcolor },
        },
        calendar: {
            left: 20,
            top: 20,
            bottom: 0,
            right: 0,
            cellSize: ['auto', 13],
            range: [startDate, endDate],
            itemStyle: {
                color: currentTheme.fangkuaicolor,
                borderWidth: 3.5,
                borderColor: currentTheme.backgroundColor,
            },
            yearLabel: { show: false },
            monthLabel: {
                nameMap: ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
                textStyle: { color: currentTheme.riqiColor }
            },
            dayLabel: {
                firstDay: 1,
                nameMap: ['日', '一', '', '三', '', '五', ''],
                textStyle: { color: currentTheme.riqiColor }
            },
            splitLine: {
                lineStyle: { color: currentTheme.xiankuangcolor }
            }
        },
        series: {
            type: 'heatmap',
            coordinateSystem: 'calendar',
            data: data,
        }
    };

    myChart.setOption(option);

    myChart.on('click', function(params) {
        if (params.componentType === 'series') {
            const post = dataMap.get(params.data[0])[0];
            window.open(post.url, '_blank').focus();
        }
    });
});
