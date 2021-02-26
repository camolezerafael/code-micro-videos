import * as React from 'react';
import {useEffect, useState} from 'react';
import DefaultTable, {makeActionsStyle, TableColumn} from '../../components/Table'

import format from "date-fns/format"
import parseISO from "date-fns/parseISO"
import {CastMember, ListResponse} from "../../util/models"
import {IconButton, MuiThemeProvider} from "@material-ui/core"
import {Link} from "react-router-dom"
import EditIcon from "@material-ui/icons/Edit"
import castMemberHttp from "../../util/http/cast-member-http"
import {useSnackbar} from "notistack"

type memberMap = {
	[key:number] : string
}

const CastMembersTypeMap: memberMap = {
	1: 'Diretor',
	2: 'Ator'
};

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
		name: 'type',
		label: 'Tipo',
		options: {
			customBodyRender(value, tableMeta, updateValue){
				return CastMembersTypeMap[value];
			}
		}
	},
	{
		name: 'created_at',
		label: 'Criado em',
		options: {
			customBodyRender(value, tableMeta, updateValue){
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
						to={`cast-members/${tableMeta.rowData[0]}/edit`}
					>
						<EditIcon/>
					</IconButton>
				)
			}
		}
	},
];

type Props = {};

const Table = (props: Props) => {

	const snackbar = useSnackbar();
	const [data, setData] = useState<CastMember[]>([]);
	const [loading, setLoading] = useState<boolean>(false);

	useEffect(() => {
		let isSubscribed = true;
		(async () => {
			setLoading(true);
			try{
				const {data} = await castMemberHttp.list<ListResponse<CastMember>>();
				if(isSubscribed){
					setData(data.data)
				}
			}catch(error) {
				console.error(error);
				snackbar.enqueueSnackbar(
					'Não foi possível carregar as informações',
					{variant: 'error'}
				)
			}finally{
				setLoading(false)
			}
		})()

		return () => {
			isSubscribed = false
		}
	}, [snackbar])

	return (
		<MuiThemeProvider theme={makeActionsStyle(columnsDefinition.length-1)}>
			<DefaultTable
				columns={columnsDefinition}
				data={data}
				title="Listagem de Elenco"
				loading={loading}
				options={{responsive: 'standard'}}
			/>
		</MuiThemeProvider>
	);
};

export default Table;