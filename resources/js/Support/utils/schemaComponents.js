import StringInput from '../../Components/Form/StringInput'
import TextInput from '../../Components/Form/TextInput'
import ToggleInput from '../../Components/Form/ToggleInput'
import SelectInput from '../../Components/Form/SelectInput'
import ImageInput from '../../Components/Form/ImageInput'
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

export default {
    hidden: {
        list: Simple,
        form: HiddenInput,
    },
    string: {
        list: Simple,
        form: StringInput,
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
        form: ImageInput,
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
}
