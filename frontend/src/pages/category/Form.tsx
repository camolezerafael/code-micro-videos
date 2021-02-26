import * as React from 'react';
import {useEffect, useState} from 'react';
import {Checkbox, FormControlLabel, TextField} from "@material-ui/core"
import {useForm} from 'react-hook-form';
import categoryHttp from "../../util/http/category-http"
import * as yup from '../../util/vendor/yup';
import {yupResolver} from "@hookform/resolvers/yup"
import {useHistory, useParams} from "react-router-dom"
import {useSnackbar} from 'notistack';
import {Category} from "../../util/models"
import SubmitActions from "../../components/SubmitActions"
import {DefaultForm} from "../../components/DefaultForm"

const validationSchema = yup.object().shape({
	name: yup.string()
		.label('Nome')
		.required(),
	is_active: yup.boolean()
		.label('Ativo?')
})


const Form = () => {

	const {
		register,
		handleSubmit,
		getValues,
		setValue,
		errors,
		reset,
		watch,
		trigger
	} = useForm<Category>({
		resolver: yupResolver(validationSchema),
		defaultValues: {
			is_active: true
		},
	});

	const snackbar = useSnackbar();
	const history = useHistory();
	const {id} = useParams<{id:string}>();
	const [category, setCategory] = useState<Category | null>(null);
	const [loading, setLoading] = useState<boolean>(false);

	useEffect(() => {
		if(!id){
			return;
		}

		let isSubscribed = true;
		(async () => {
			setLoading(true);
			try {
				const {data} = await categoryHttp.get(id);
				if(isSubscribed) {
					setCategory(data.data);
					reset(data.data);
				}
			}catch (error) {
				console.error(error);
				snackbar.enqueueSnackbar('Não foi possível carregar as informações', {
					variant: 'error',
				});
			}finally {
				setLoading(false);
			}
		})()

		return () => {
			isSubscribed = false
		}

	}, [id, reset,snackbar])

	useEffect(() => {
		register({name:'is_active'})
	}, [register])

	async function onSubmit(formData: any, event: any) {
		setLoading(true);

		try {
			const http = !category
				? categoryHttp.create(formData)
				: categoryHttp.update(category.id, formData);
			const {data} = await http;

			snackbar.enqueueSnackbar('Categoria salva com sucesso',
		{ variant: 'success' }
			);
			setTimeout(()=>{
				event
					? (
						id ? history.replace(`/categories/${data.data.id}/edit`)
							: history.push(`/categories/${data.data.id}/edit`)
					)
					: history.push('/categories')
			});
		}catch (error) {
			console.error(error);
			snackbar.enqueueSnackbar(
				'Não foi possível salvar a categoria',
				{ variant: 'error' }
				);
		}finally {
			setLoading(false);
		}
	}

	return (
		<DefaultForm GridItemProps={{xs:12, md:6}} onSubmit={handleSubmit(onSubmit)}>
			<TextField
				name="name"
				label="Nome"
				fullWidth
				variant={"outlined"}
				inputRef={register}
				disabled={loading}
				error={errors.name !== undefined}
				helperText={errors.name && errors.name.message}
				InputLabelProps={{shrink: true}}
			/>
			<TextField
				name="description"
				label="Descrição"
				multiline
				rows="4"
				fullWidth
				variant={"outlined"}
				margin={"normal"}
				inputRef={register}
				disabled={loading}
				InputLabelProps={{shrink: true}}
			/>
			<FormControlLabel
				disabled={loading}
				control={
					<Checkbox
						name={"is_active"}
						color={"primary"}
						onChange={
							() => setValue('is_active', !getValues()['is_active'])
						}
						checked={ watch( 'is_active' ) }
					/>
				}
				label={'Ativo?'}
				labelPlacement={'end'}
			/>
			<SubmitActions
				disabledButtons={loading}
				handleSave={ () =>
					trigger().then(isValid => {
						isValid && onSubmit(getValues(), null)
					})
				}
			/>
		</DefaultForm>
	);
};


export default Form;