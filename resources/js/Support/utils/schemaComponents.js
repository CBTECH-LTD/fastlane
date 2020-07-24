import StringInput from '../../Components/Form/StringInput'
import TextInput from '../../Components/Form/TextInput'
import ToggleInput from '../../Components/Form/ToggleInput'
import SelectInput from '../../Components/Form/SelectInput'
import ImageInput from '../../Components/Form/ImageInput'
import DateTimeInput from '../../Components/Form/DateTimeInput'
import RichEditorInput from '../../Components/Form/RichEditorInput'
import Simple from '../../Components/List/Simple'
import Image from '../../Components/List/Image'

export default {
    string: {
        list: Simple,
        form: StringInput,
    },
    text: {
        list: Simple,
        form: TextInput,
    },
    toggle: {
        list: Simple,
        form: ToggleInput,
    },
    select: {
        list: Simple,
        form: SelectInput,
    },
    image: {
        list: Image,
        form: ImageInput,
    },
    file: {
        list: Simple,
        form: StringInput,
    },
    date: {
        list: Simple,
        form: DateTimeInput,
    },
    richEditor: {
        list: Simple,
        form: RichEditorInput,
    },
}
