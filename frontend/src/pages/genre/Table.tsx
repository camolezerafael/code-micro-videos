import * as React from 'react';
import {useEffect, useState} from 'react';
import DefaultTable, {makeActionsStyle, TableColumn} from '../../components/Table'
import {Chip, IconButton, MuiThemeProvider} from "@material-ui/core"

import format from "date-fns/format"
import parseISO from "date-fns/parseISO"
import {Genre, ListResponse} from "../../util/models"
import {Link} from "react-router-dom"
import EditIcon from "@material-ui/icons/Edit"
import {useSnackbar} from "notistack"
import genreHttp from "../../util/http/genre-http"


const columnsDefinition: TableColumn[] = [
	{
		name: 'id',
		label: 'ID',
		width: '30%',
		options: {
			sort: false
		}
	},
	{
		name: 'name',
		label: 'Nome'
	},
	{
		name: 'categories',
		label: 'Categoria(s)',
		options: {
			customBodyRender(values, tableMeta, updateValue) {
				values = values.map((value: any) => {
					return <Chip label={value.name} variant="outlined" color="primary" size="small" clickable={true} component="a" href="#" style={{margin: "0px 2px"}}/>
				});
				return values;
			},
		},
	},
	{
		name: 'is_active',
		label: 'Ativo?',
		options: {
			customBodyRender(value, tableMeta, updateValue) {
				return value ? <Chip label="Sim" color="primary"/> : <Chip label="Não" color="secondary"/>;
			}
		}
	},
	{
		name: 'created_at',
		label: 'Criado em',
		options: {
			customBodyRender(value, tableMeta, updateValue) {
				return <span>{format(parseISO(value), 'dd/MM/yyyy')}</span>
			}
		}
	},
	{
		name: 'actions',
		label: 'Ações',
		width: '13%',
		options: {
			sort: false,
			customBodyRender(value, tableMeta) {
				return (
					<IconButton
						color={'secondary'}
						component={Link}
						to={`genres/${tableMeta.rowData[0]}/edit`}
					> <EditIcon/> </IconButton>
				)
			}
		}
	},
];

type Props = {};

const Table = (props: Props) => {

	const snackbar = useSnackbar();
	const [data, setData] = useState<Genre[]>([]);
	const [loading, setLoading] = useState<boolean>(false);

	useEffect(() => {
		let isSubscribed = true;
		(async () => {
			setLoading(true);
			try {
				const {data} = await genreHttp.list<ListResponse<Genre>>();
				if (isSubscribed) {
					setData(data.data);
				}
			} catch (error) {
				console.error(error);
				snackbar.enqueueSnackbar(
					'Não foi possível carregar as informações',
					{variant: 'error'}
				)
			} finally {
				setLoading(false);
			}
		})()

		return () => {
			isSubscribed = false
		}
	}, [snackbar])

	return (
		<MuiThemeProvider theme={makeActionsStyle(columnsDefinition.length - 1)}> <DefaultTable
			columns={columnsDefinition}
			data={data}
			title="Listagem de Gêneros"
			loading={loading}
			options={{responsive: 'standard'}}
		/> </MuiThemeProvider>
	);
};

export default Table;