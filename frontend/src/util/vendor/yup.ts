import {setLocale} from 'yup';

const ptBR = {
	mixed:{
		required: '${path} é requerido'
	},
	string:{
		max: '${path} precisa ter no máximo ${max} caracteres',
		min: '${path} precisa ter no mínimo ${min} caracteres',
	},
	number:{
		min: '${path} precisa ser no mínimo ${min}',
		max: '${path} precisa ser no máximo ${min}',
	}
};

setLocale(ptBR);

export * from 'yup';