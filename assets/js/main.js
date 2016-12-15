$(document).ready(function() {

});

function trafficByIsp(container, from, to) {
	$.getJSON('json.php?name=traffbyisp&time='+from+','+to, function (data) {
		var total 	= [],
			vnpt 	= [],
			viettel = [],
			fpt 	= [],
			dataLength = data['date'].length,
			groupingUnits = [
				['week', [1]],
				['month', [1, 2, 3, 4, 6]]
			],
			i = 0;
		for (i; i < dataLength; i += 1) {
			total.push([
				Date.parse(data['date'][i]),
				data['total'][i],
			]);
			if ( typeof data['vnpt'] !== 'undefined' && data['vnpt'].length > 0 ) {
				vnpt.push([
					Date.parse(data['date'][i]),
					data['vnpt'][i],
				]);
			}
			if ( typeof data['viettel'] !== 'undefined' && data['viettel'].length > 0 ) {
				viettel.push([
					Date.parse(data['date'][i]),
					data['viettel'][i],
				]);
			}
			if ( typeof data['fpt'] !== 'undefined' && data['fpt'].length > 0 ) {
				fpt.push([
					Date.parse(data['date'][i]),
					data['fpt'][i],
				]);
			}
		}

		// create the chart
		$('#'+container).highcharts('StockChart', {
			title: {
				text: 'Total Download Traffic per CDN Providers'
			},
			subtitle: {
				text: '---------'
			},
			rangeSelector: {
				allButtonsEnabled: true,
				buttons: [{
					type: 'day',
					count: 10,
					text: 'Day',
					dataGrouping: {
						forced: true,
						units: [['day', [1]]]
					}
				}, {
					type: 'week',
					count: 4,
					text: 'Week',
					dataGrouping: {
						forced: true,
						units: [['week', [1]]]
					}
				}, {
					type: 'month',
					text: 'Month',
					dataGrouping: {
						forced: true,
						units: [['month', [1]]]
					}
				}],
				buttonTheme: {
					width: 60
				},
				selected: 0
			},
			credits: {
				enabled: false
			},
			yAxis:
				{
					labels: {
						align: 'left',
					},
					title: {
						text: 'Traffic (bytes)'
					},
					height: '100%',
					lineWidth: 2
				},

			series: [
				{
					name: 'Total',
					data: total,
					dataGrouping: {
						units: groupingUnits
					},
					marker: {
						enabled: true,
						radius: 3
					},
				},
				{
					name: 'Vnpt',
					data: vnpt,
					dataGrouping: {
						units: groupingUnits
					},
					marker: {
						enabled: true,
						radius: 3
					},
				},
				{
					name: 'Viettel',
					data: viettel,
					dataGrouping: {
						units: groupingUnits
					},
					marker: {
						enabled: true,
						radius: 3
					},
				},
				{
					name: 'Fpt',
					data: fpt,
					dataGrouping: {
						units: groupingUnits
					},
					marker: {
						enabled: true,
						radius: 3
					},
				},
			]
		});
	});
}

function trafficByCus(container, from, to, cus) {
	$.getJSON('json.php?name=traffbyisp&time='+from+','+to+'&cus='+cus, function (data) {
		var total 	= [],
			vnpt 	= [],
			viettel = [],
			fpt 	= [],
			dataLength = data['date'].length,
			groupingUnits = [
				['week', [1]],
				['month', [1, 2, 3, 4, 6]]
			],
			i = 0;
		for (i; i < dataLength; i += 1) {
			total.push([
				Date.parse(data['date'][i]),
				data['total'][i],
			]);
			if ( typeof data['vnpt'] !== 'undefined' && data['vnpt'].length > 0 ) {
				vnpt.push([
					Date.parse(data['date'][i]),
					data['vnpt'][i],
				]);
			}
			if ( typeof data['viettel'] !== 'undefined' && data['viettel'].length > 0 ) {
				viettel.push([
					Date.parse(data['date'][i]),
					data['viettel'][i],
				]);
			}
			if ( typeof data['fpt'] !== 'undefined' && data['fpt'].length > 0 ) {
				fpt.push([
					Date.parse(data['date'][i]),
					data['fpt'][i],
				]);
			}
		}

		// create the chart
		$('#'+container).highcharts('StockChart', {
			title: {
				text: 'Total Customer Download Traffic by CDN Providers'
			},
			subtitle: {
				text: '---------'
			},
			rangeSelector: {
				allButtonsEnabled: true,
				buttons: [{
					type: 'day',
					count: 10,
					text: 'Day',
					dataGrouping: {
						forced: true,
						units: [['day', [1]]]
					}
				}, {
					type: 'week',
					count: 4,
					text: 'Week',
					dataGrouping: {
						forced: true,
						units: [['week', [1]]]
					}
				}, {
					type: 'month',
					text: 'Month',
					dataGrouping: {
						forced: true,
						units: [['month', [1]]]
					}
				}],
				buttonTheme: {
					width: 60
				},
				selected: 0
			},
			credits: {
				enabled: false
			},
			yAxis:
				{
					labels: {
						align: 'left',
					},
					title: {
						text: 'Traffic (bytes)'
					},
					height: '100%',
					lineWidth: 2
				},

			series: [
				{
					name: 'Total',
					data: total,
					dataGrouping: {
						units: groupingUnits
					},
					marker: {
						enabled: true,
						radius: 3
					},
				},
				{
					name: 'Vnpt',
					data: vnpt,
					dataGrouping: {
						units: groupingUnits
					},
					marker: {
						enabled: true,
						radius: 3
					},
				},
				{
					name: 'Viettel',
					data: viettel,
					dataGrouping: {
						units: groupingUnits
					},
					marker: {
						enabled: true,
						radius: 3
					},
				},
				{
					name: 'Fpt',
					data: fpt,
					dataGrouping: {
						units: groupingUnits
					},
					marker: {
						enabled: true,
						radius: 3
					},
				},
			]
		});
	});
}