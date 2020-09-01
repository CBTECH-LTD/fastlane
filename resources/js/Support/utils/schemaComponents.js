import StringInput from '../../Components/Form/StringInput'
import TextInput from '../../Components/Form/TextInput'
import ToggleInput from '../../Components/Form/ToggleInput'
import SelectInput from '../../Components/Form/SelectInput'
import DateTimeInput from '../../Components/Form/DateTimeInput'
import RichEditorInput from '../../Components/Form/RichEditorInput'
import PasswordInput from '../../Components/Form/PasswordInput'
import FileInput from '../../Components/Form/FileInput'
import HiddenInput from '../../Components/Form/HiddenInput'
import YearInput from '../../Components/Form/YearInput'
import Simple from '../../Components/List/Simple'
import Image from '../../Components/List/Image'
import Select from '../../Components/List/Select'
import Toggle from '../../Components/List/Toggle'
import DateTime from '../../Components/List/DateTime'
import File from '../../Components/List/File'
import BlockEditorInput from '../../Components/Form/BlockEditor/BlockEditorInput'
import CurrencyInput from '../../Components/Form/CurrencyInput'
import NumberInput from '../../Components/Form/NumberInput'

export default {
    hidden: {
        list: Simple,
        form: HiddenInput,
    },
    string: {
        list: Simple,
        form: StringInput,
    },
    number: {
        list: Simple,
        form: NumberInput,
    },
    currency: {
        list: Simple,
        form: CurrencyInput,
    },
    password: {
        list: Simple,
        form: PasswordInput,
    },
    text: {
        list: Simple,
        form: TextInput,
    },
    toggle: {
        list: Toggle,
        form: ToggleInput,
    },
    select: {
        list: Select,
        form: SelectInput,
    },
    image: {
        list: Image,
        form: FileInput,
    },
    file: {
        list: File,
        form: FileInput,
    },
    date: {
        list: DateTime,
        form: DateTimeInput,
    },
    year: {
        list: DateTime,
        form: YearInput,
    },
    richEditor: {
        list: Simple,
        form: RichEditorInput,
    },
    blockEditor: {
        list: Simple,
        form: BlockEditorInput,
    }
}
