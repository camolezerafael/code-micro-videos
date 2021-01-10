import * as React from 'react';
import {useEffect, useState} from 'react';
import MUIDataTable, {MUIDataTableColumn} from "mui-datatables"
import {httpVideo} from "../../util/http"

import format from "date-fns/format"
import parseISO from "date-fns/parseISO"

const membersType = ['Diretor', 'Ator'];

const columnsDefinition: MUIDataTableColumn[] = [
	{
		name: 'name',
		label: 'Nome'
	},
	{
		name: 'type',
		label: 'Tipo',
		options: {
			customBodyRender(value, tableMeta, updateValue){
				return membersType[value -1]
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
	}
];

type Props = {};

const Table = (props: Props) => {

	const [data, setData] = useState([]);

	useEffect(() => {
		httpVideo.get('cast_members').then(
			response => setData(response.data.data)
		)
	}, [])

	return (
		<MUIDataTable
			columns={columnsDefinition}
			data={data}
			title="Listagem de Elenco"
		/>
	);
};

export default Table;